<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Selector;

class ArticleRepository{

    public array $articleData = [];
    public array $allowedArticles = ['allwin', 'samuel', 'isama', 'isamanh', 'isamanw', 'angel', 'mry', 'white', 'terror', 'hyperion', 'demoni'];
    private array $buffer = [];

    /**
     * getCurrentArticle
     *
     * @param Mlkali\Sa\Database\DB $db
     * @return mixed
     */
    public function getCurrentArticle()
    {
        $selector = new Selector();
        $db = new DB();   
       
        if(in_array(mb_strtolower($selector->article), $this->allowedArticles)){

            $articleId = $selector->article.'|'.$selector->page;

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
                    return $this->articleData['article_body'];
                break;             
            }
        }
        return null;
    }

    public function exist(string $articleId): bool
    {
        $stmt = $this->db->query
                ->from('articles')
                ->select('article_id')
                ->where('article_id', $articleId);
        
        $result = $stmt->fetch('article_id');
        
        if(!$result){
            return false;
        }
            return true;
    }
    
    public function update(): bool
    {
        if(!empty($this->buffer['id'])){

            $set = [
                'article_body' => $this->buffer['body'], 
                'article_chapter' => $this->buffer['chapter']
            ];

            $stmt = $this->db->query
                    ->update('articles')
                    ->set($set)
                    ->where('article_id', $this->buffer['id'])
                    ->execute();
            
            if($stmt){
                return true;
            }
                return false;        
        }
        return false;
    }
    
    public function add(): bool
    {
        if(in_array(strtolower($this->selector->article), $this->allowedArticles)){

            $values = [
                'article_chapter' => $this->buffer['chapter'],
                'article_body' => $this->buffer['body'],
                'article_id' =>  $this->buffer['id']
            ];

            $stmt = $this->db->query
                    ->insertInto('articles')
                    ->values($values)
                    ->execute();
            
            if($stmt){
                return true;
            }
            return false;        
        }
        return false;
    }
    
    public function remove(string $articleId): bool
    {
        if(in_array(strtolower($this->selector->article), $this->allowedArticles)){

            $stmt = $this->db->query
                    ->deleteFrom('articles')
                    ->where('article_id', $articleId)
                    ->execute();
            
            if($stmt){
                return true;
            }
            return false;        
        }
        return false;
    }

    public function readyToSet(string $key, $params): self
    {
        $this->buffer[$key] .= $params;

        return $this;
    }
}