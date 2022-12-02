<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Database\Repository\ArticleRepository;

class Article extends ArticleRepository{

    public function __construct(
        private DB $db, 
        private Selector $selector,
        private ?string $articleChapter = null,
        private ?string $articleId = null,
        private ?array $articleBody = [],
        private array $buffer = []
    )
    {
    }

    public function getArticleId(): ?string
    {
        $this->articleId = $this->getCurrentArticle($this->db)->get('id');

        return $this->articleId;
    }

    public function setArticleId(string $articleId): self
    {
        $this->readyToSet('id', $articleId);

        return $this;
    }

    public function getArticleChapter(): ?string
    {
        $this->articleChapter = $this->getCurrentArticle($this->db)->get('chapter');
        
        return $this->articleChapter;
    }

    public function setArticleChapter(?string $chapter = null): self
    {
        $this->readyToSet('chapter', $chapter);

        return $this;
    }

    public function getArticleBody(): ?array
    {
        $this->articleBody = $this->getCurrentArticle($this->db)->get('body');

        return $this->articleBody;
    }

    public function setArticleBody(string $body): self
    {
        $this->readyToSet('body', $body);

        return $this;
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

    private function readyToSet(string $key,$params): self
    {
        $this->buffer[$key] .= $params;

        return $this;
    }
}
