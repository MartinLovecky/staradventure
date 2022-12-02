<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;

class ArticleController{
   
    public function __construct(
        private Request $request, 
        private Article $article
    )
    {
    }

    public function update(): Response
    {
        $articleId = $this->request->articleName.$this->request->articlePage;

        if($this->article->exist($articleId)){

            $chapter = !empty($this->request->chapter) ? $this->request->chapter : null;
            $article_body = isset($this->request->editor1) ? json_encode(['article_body' =>$this->request->editor1]) : '{"article_body":"error"}'; 
            
            $this->article
                ->setArticleId($articleId)
                ->setArticleChapter($chapter)
                ->setArticleBody($article_body)
                ->update();

            return new Response('/update'.'/'.$this->request->articleName.'/'.$this->request->articlePage.'?message=','success.Příběh '.$articleId.' upraven');
        }

        return new Response('/update?message=','warning.Stránka '.$articleId.' neexistuje použite <a href="/create/'.$this->request->articleName.'/'.$this->request->articlePage.'">create</a>');
    }

    public function create(): Response
    {
        $articleId = $this->request->articleName.$this->request->articlePage;

        if($this->article->exist($articleId) === false){

            $chapter = !empty($this->request->chapter) ? $this->request->chapter : null;
            $article_body = isset($this->request->editor1) ? json_encode(['article_body' =>$this->request->editor1]) : '{"article_body":"empty"}'; 

            $this->article
                ->setArticleId($articleId)
                ->setArticleChapter($chapter)
                ->setArticleBody($article_body)
                ->add();
        
            return new Response('/update?message=','success.Stránka '.$articleId.' vytvořena');
        }

        return new Response('/update?message=','warning.Stránka '.$articleId.' již existuje použite <a href="/update/'.$this->request->articleName.'/'.$this->request->articlePage.'">update</a>');
    }

    public function delete(): Response
    {
        $articleId = $this->request->articleName.$this->request->articlePage;

        if($this->article->exist($articleId)){

            $this->article->remove($articleId);
    
            return new Response('/update?message=','success.Stránka '.$articleId.' smazána');
        }

        return new Response('/update?message=','warning.Stránka '.$articleId.' neexistuje použite <a href="/create/'.$this->request->articleName.'/'.$this->request->articlePage.'">create</a>');
    }
}