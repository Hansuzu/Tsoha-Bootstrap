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
    
    public static function viewArticle1($languageShortCode, $articleName){
        $language=Language::findByShortcode($languageShortCode);
        $articles=array();
        if ($language) {
            $articles=Article::findByNameAndLanguage($articleName, $language->id);
            foreach($articles as $article){
                $article->load();
            }
        }
        if (count($articles)==1){
            $versions=Article::findByAbstractId($articles[0]->abstract_id);
            foreach($versions as $version){
                $version->load();
            }
            View::make('article.html', array("article"=>$articles[0], "versions"=>$versions));
        }else{
            View::make('article_confusion.html', array("articles"=>$articles));
        }
    }
    
    public static function viewArticle2($languageShortCode, $articleName, $articleId) {
        $articles=array();
        $article=Article::findById($articleId);
        $versions=array();
        if ($article){
            $versions=Article::findByAbstractId($article->abstract_id);
            $article->load();
            foreach($versions as $version){
                $version->load();
            }
            View::make('article.html', array("article"=>$article, "versions"=>$versions));
        }else{
            View::make('article_confusion.html', array("articles"=>array()));
        }
    }
}

?>