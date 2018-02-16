<?php
class ArticleController extends BaseController{
    public static function index(){
        if (isset($_GET["words"])){
            $articles=Article::search($_GET["words"]);
            foreach($articles as $article){
                $article->load();
            }
            View::make('article_search.html', array("results"=>$articles,"search"=>true));
        }else{
            View::make('article_search.html');
        }
    }
    
    
    public static function viewArticle($article, $version=-1){
        $versions=Article::findByAbstractId($article->abstract_id);
        $article->load($version);
        foreach($versions as $version){
            $version->load();
        }
        $suparticles=ArticleSuperclass::findSupArticles($article->id);
        $subarticles=ArticleSuperclass::findSubArticles($article->id);

        $article->version->contents_motor();
        View::make('article.html', array("article"=>$article, "versions"=>$versions, "suparticles"=>$suparticles, "subarticles"=>$subarticles));
    }
    
    public static function viewArticleByNameAndLanguage($languageShortCode, $articleName){
        $language=Language::findByShortcode($languageShortCode);
        $articles=array();
        if ($language) {
            $articles=Article::findByNameAndLanguage($articleName, $language->id);
            foreach($articles as $article){
                $article->load();
            }
        }
        if (count($articles)==1){
            self::viewArticle($articles[0]);
        }else{
            View::make('article_confusion.html', array("articles"=>$articles));
        }
    }
    
    
    public static function viewArticleById($articleId) {
        $articles=array();
        $article=Article::findById($articleId);
        if ($article){
            self::viewArticle($article);
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    public static function viewArticleByVersionId($versionId) {
        $article=Article::findById(ArticleVersion::getArticleId($versionId));
        if ($article){
            self::viewArticle($article, $versionId);
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    
    public static function editArticle($version_id) {
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())   Redirect::to("/page/version/".$version_id, array("error_message"=>"Sinulla ei ole artikkeleiden muokkausoikeutta."));
        
        $version=ArticleVersion::findById($version_id);
        if ($version){
            $article=Article::findById($version->article_id);
            if ($article){
                $article->load();
                if ($article->isReadonly() && !$user->isModerator()){
                    Redirect::to("/page/version/".$version_id, array("error_message"=>"Artikkeli on readonly-tilassa :3"));
                }
                View::make('article_edit.html', array("article"=>$article, "version"=>$version, "versions"=>array()));
            }else{
                View::make('article_confusion.html', array("articles"=>array()));
            }
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
    
    public static function createArticle() {
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())   Redirect::to("/page/version/".$version_id);
        
        $newArticle=new Article(array());
        $newVersion=new ArticleVersion(array("id"=>-1));
        $languages=Language::all();
        View::make('article_edit.html', array("article"=>$newArticle, "version"=>$newVersion, "languages"=>$languages, "abstract_id"=>"-1"));
    }
    
    public static function lCreateArticle($abstract_id) { //Create new article about a topic that exist in another language 
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (! $user->editAllowed())   Redirect::to("/page/version/".$version_id);
        
        $newArticle=new Article(array());
        $newVersion=new ArticleVersion(array("id"=>-1));
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
        $articleVersion=ArticleVersion::findById($parent_version);
        if ($articleVersion){
            $article=Article::findById($articleVersion->article_id);
            if ($article){
                $article->name=$title;
                $article->language_id=$language->id;
                
                $articleVersion->contents=$contents;
                $articleVersion->user_id=1;
                
                $errors=array_merge(array_merge($errors, $article->errors(), $articleVersion->errors()));
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
        $errors=array();
        $article=new Article(array("abstract_id" => $abstract_id, 
                                    "language_id" => $language->id,
                                    "readonly" => 0x00,
                                    "name"=>$title));
        $errors=array_merge($errors, $article->errors());
        if (count($errors)==0){
            $article->saveAsNew();
            $version=new ArticleVersion(array("article_id" => $article->id, "parent_id" => null, "user_id" => 1, "contents" => $contents));
            $errors=array_merge($errors, $version->errors());
            if (count($errors)==0){
                $version->saveNewVersion();
                $version->setAsActiveVersion();
                Redirect::to('/page/view/'.$language->shortcode."/".$title."/".$article->id, array('message' => '10-4'));
            }
        }
        return $errors;
    }
    
    
    public static function saveArticle(){
        self::check_logged_in();
        
        $errors=array();
        $parent_version="0";
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
                }else $errors[]="Abstraktia artikkelia ei ole olemassa (debug: 103)";
            }else $errors[]="Kieltä ei ole olemassa (debug: 104)";
        }else $errors[]="Lomakkeessa on jotain virheellistä (debug: 105)";
        $error_message="Jotakin meni pieleen, mutta minä en tosiaankaan tiedä, mitä. (Toisin sanoen tapahtui virhe, joka ei vielä osaa kertoa omaa nimeänsä.)";
        if (count($errors)) $error_message=$errors[0];
        Redirect::to('/page/version/'.$_POST["parent_version"], array("error_message"=>$error_message));
    }
    
}

?>