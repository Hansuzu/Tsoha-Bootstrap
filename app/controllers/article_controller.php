<?php
class ArticleController extends BaseController{

    public static function index(){ //Main search page
        if (isset($_GET["words"])){
            $articles=Article::search($_GET["words"]);
            foreach($articles as $article){
                $article->load(-2); //Loads the language information about this article (-2 tells that no ArticleVersion will be loaded)
            }
            View::make('article_search.html', array("results"=>$articles,"search"=>true));
        }else{
            View::make('article_search.html');
        }
    }
    
    
    
    //This will be called by viewArticleByNameAndLanguage, viewArticleById or viewArticleByVersionId
    //Creates the view-page of article when given the $article (instance of class Article) 
    //and $version (integer, -1 means that active version will be loaded)
    public static function viewArticle($article, $version=-1){ 
        $article->load($version);
        $article->version->contents_motor(); //generate the html
        
        //Get versions of this article on different languages
        $versions=Article::findByAbstractId($article->abstract_id);
        foreach($versions as $version){
            $version->load();
        }
        //Find suparticles and subarticles
        $suparticles=ArticleSuperclass::findSupArticles($article->id);
        $subarticles=ArticleSuperclass::findSubArticles($article->id);
        
        View::make('article.html', array("article"=>$article, "versions"=>$versions, "suparticles"=>$suparticles, "subarticles"=>$subarticles));
    }
    
    
    
    public static function viewArticleByNameAndLanguage($languageShortCode, $articleName){
        $articles=array();
        if ($language=Language::findByShortcode($languageShortCode)) {
            $articles=Article::findByNameAndLanguage($articleName, $language->id);
            foreach($articles as $article){
                $article->load(-2);
            }
        }
        if (count($articles)==1){
            self::viewArticle($articles[0]);
        }else{ //Article doesn't exist or many articles satisfy the condition (which shouldn't happen)
            View::make('article_confusion.html', array("articles"=>$articles));
        }
    }
    
    public static function viewArticleById($articleId) {
        if ($article=Article::findById($articleId)){
            self::viewArticle($article);
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    
    public static function viewArticleByVersionId($versionId) {
        if ($article=Article::findById(ArticleVersion::getArticleId($versionId))){
            self::viewArticle($article, $versionId);
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    
    
    public static function editArticle($version_id) { //view Article edit-page
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())  Redirect::to("/page/version/".$version_id, array("error_message"=>"Editing articles is not allowed non your account."));
        
        if ($version=ArticleVersion::findById($version_id)){
            if ($article=Article::findById($version->article_id)){
                $article->load();
                if ($article->isReadonly() && !$user->isModerator()){
                    Redirect::to("/page/version/".$version_id, array("error_message"=>"This article is read-only <3"));
                }
                View::make('article_edit.html', array("article"=>$article, "version"=>$version, "versions"=>array()));
            }else{
                View::make('article_confusion.html', array("articles"=>array()));
            }
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    
    public static function createArticle() { //view new-Article edit-page 
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())   Redirect::to("/page/version/".$version_id);
        
        $newArticle=new Article(array());
        $newVersion=new ArticleVersion(array("id"=>-1));
        $languages=Language::all();
        View::make('article_edit.html', array("article"=>$newArticle, "version"=>$newVersion, "languages"=>$languages, "abstract_id"=>"-1"));
    }
    
    public static function createArticleAboutExistingTopic($abstract_id) { //Create new article about a topic that exist in another language 
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())   Redirect::to("/page/version/".$version_id);
        
        $newArticle=new Article(array());
        $newVersion=new ArticleVersion(array("id"=>-1));
        
        //Get a list of languages on which this article doesn't exist
        //TODO nicer way to do this
        $languages_0=Language::all(); 
        $versions=Article::findByAbstractId($abstract_id);
        $languages=array();
        foreach($languages_0 as $language){
            $ok=true;
            foreach( $versions as $version){
                if ($version->language_id == $language->id){
                    $ok=false;
                    break;
                }
            }
            if ($ok) $languages[]=$language;
        }
        
        View::make('article_edit.html', array("article"=>$newArticle, "version"=>$newVersion, "languages"=>$languages, "abstract_id"=>$abstract_id));
    }
    
    
    
    
    
    private static function updateArticle($abstract_id, $title, $contents, $language, $parent_version){ //SaveArticle calls this
        $errors=array();
        if ($articleVersion=ArticleVersion::findById($parent_version)){ //Get previous version and edit on it
            if ($article=Article::findById($articleVersion->article_id)){
                $article->name=$title;
                $article->language_id=$language->id; //Theoretically language could be changed, but edit-page doesn't currently support it
                
                $articleVersion->contents=$contents;
                $articleVersion->user_id=self::get_user_logged_in()->id;
                
                $errors=array_merge($errors, $article->errors(), $articleVersion->errors());
                if (count($errors)==0){
                    $article->update();
                    $articleVersion->saveNewVersion();
                    $articleVersion->setAsActiveVersion();
                    Redirect::to('/page/view/'.$language->shortcode."/".$title."/".$article->id, array('message' => '10-4'));
                }
            }else $errors[]="Artikkelia ei löytynyt (debug: 101)";
        }else $errors[]="Artikkeliversiota ei löytynyt (debug: 102)";
        return $errors;
    }
    
    private static function newArticle($abstract_id, $title, $contents, $language){ //SaveArticle Calls this
        $article=new Article(array("abstract_id" => $abstract_id, 
                                    "language_id" => $language->id,
                                    "readonly" => 0x00,
                                    "name"=>$title));
        $errors=$article->errors();
        if (count($errors)==0){ //TODO: check also errors in version before saving article...
            $article->saveAsNew();
            $version=new ArticleVersion(array("article_id" => $article->id, "parent_id" => null, "user_id" => self::get_user_logged_in()->id, "contents" => $contents));
            $errors=array_merge($errors, $version->errors());
            if (count($errors)==0){
                $version->saveNewVersion();
                $version->setAsActiveVersion();
                Redirect::to('/page/view/'.$language->shortcode."/".$title."/".$article->id, array('message' => '10-4'));
            }
        }
        return $errors;
    }
    
    
    public static function saveArticle(){ //Article save page
        self::check_logged_in();
        
        $errors=array();
        $parent_version=-1;
        if (isset($_POST["parent_version"]) and isset($_POST["title"]) and isset($_POST["article"]) and isset($_POST["language"]) and isset($_POST["abstract_id"])){
            $parent_version=$_POST["parent_version"];
            $title=$_POST["title"];
            $contents=$_POST["article"];
            $language_shortcode=$_POST["language"];
            $abstract_id=$_POST["abstract_id"];
            
            $language=Language::findByShortcode($language_shortcode);
            if ($language){
                if ($abstract_id=="-1")  $abstract_article=AbstractArticle::newAbstractArticle($abstract_id);
                else                     $abstract_article=AbstractArticle::findById($abstract_id);
                
                if ($abstract_article){
                    if ($parent_version=="-1"){ //new article
                        $errors=array_merge($errors, self::newArticle($abstract_article->id, $title, $contents, $language));
                    }else{ //update old article
                        $errors=array_merge($errors, self::updateArticle($abstract_article->id, $title, $contents, $language, $parent_version));
                    }
                }else $errors[]="Abstract article doesn't exist (debug: 103)";
            }else $errors[]="The language doesn't exist (debug: 104)";
        }else $errors[]="There is some error in the form. (debug: 105)";
        $error_message="Something went terribly wrong, but I have no idea what. (Error which doesn't yet know how to tell its name)";
        if (count($errors)) $error_message=$errors[0]; //show only first error
        if ($parent_version>=0)  Redirect::to('/page/version/'.$parent_version, array("error_message"=>$error_message));
        else Redirect::to('page/', array("error_message"=>$error_message));
    }
    
    
    
    
    public static function checkSubmittedMessages($article){
        $errors=array();
        if (isset($_POST["message"])){
            $user=self::get_user_logged_in();
            if ($user){
                if ($user->messagesAllowed()){
                    $message=new Message(array("article_id"=>$article->id, "user_id"=>$user->id, "message"=>$_POST["message"]));
                    $errors=$message->errors();
                    if (count($errors)==0){
                        $message->saveAsNew();
                    }
                }else $errors[]="Your account has no rights to write messages here.";
            }else $errors[]="You must be logged in to write messges";
        }
        return $errors;
    }
    //Article discussion page
    public static function viewDiscussion($article){
        $errors=self::checkSubmittedMessages($article);
        $article->load(-2);
        $messages=Message::findAllInArticle($article->id);
        //Get discussions on different languages
        $versions=Article::findByAbstractId($article->abstract_id);
        foreach($versions as $version){
            $version->load();
        }
        View::make("article_discussion.html", array("messages"=>$messages, "versions"=>$versions, "article"=>$article, "error_messages"=>$errors));
    }
    public static function viewDiscussionByNameAndLanguage($languageShortCode, $articleName){
        $articles=array();
        if ($language=Language::findByShortcode($languageShortCode)) {
            $articles=Article::findByNameAndLanguage($articleName, $language->id);
            foreach($articles as $article){
                $article->load(-2);
            }
        }
        if (count($articles)==1){
            self::viewDiscussion($articles[0]);
        }else{
            View::make('article_confusion.html', array("articles"=>$articles));
        }
    }
    
    public static function viewDiscussionById($articleId) {
        if ($article=Article::findById($articleId)){
            self::viewDiscussion($article);
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
}

?>