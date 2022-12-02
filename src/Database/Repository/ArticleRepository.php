<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Support\Selector;

class ArticleRepository{

    public array $articleData = [];
    public array $allowedArticles = ['allwin', 'samuel', 'isama', 'isamanh', 'isamanw', 'angel', 'mry', 'white', 'terror', 'hyperion', 'demoni'];
    
    /**
     * getCurrentArticle
     *
     * @param Mlkali\Sa\Database\DB $db
     * @return mixed
     */
    public function getCurrentArticle($db)
    {
        $selector = new Selector();
       
        if(in_array(strtolower($selector->article), $this->allowedArticles)){

            $articleId = $selector->article.$selector->page;

            $stmt = $db->query
                    ->from('articles')
                    ->where('article_id', $articleId);
            
            $data = $stmt->fetch();

            if($data){
                $this->articleData = $data;
            }
        }

        return $this;
    }
        
    /**
     * get id|chapter|body
     *
     * @param string $item
     * @return $articleData
     */
    public function get(string $item) 
    {
        if(!empty($this->articleData)){
            switch($item){
                case 'id':
                    return $this->articleData['article_id'];
                break;
                case 'chapter':
                    return $this->articleData['article_chapter'];  
                break;
                case 'body':
                    return json_decode($this->articleData['article_body'], true);
                break;             
            }
        }
        return null;
    }
}