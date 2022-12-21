<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Support\Selector;

class ArticleRepository
{

    public function __construct(
        private Selector $selector,
        private DB $db,
        public array $articleData = [],
        private ?string $articleID
    ) {
        $this->articleID = ($this->selector->article && $this->selector->page) ? $this->selector->article . '|' . $this->selector->page : null;
    }

    /**
     * Can get specific column for articleID or all columns 
     * @param string|null $articleID is handled by selector 
     * @param string|null $column array if null, otherwise $column value
     * @return string|null
     */
    public function getCurrentArticle(?string $column = null): string|null
    {
        if ($this->allowedArticle() && $this->exist($this->articleID)) {

            $stmt = $this->db->query
                ->from('articles')
                ->select($column)
                ->where('article_id', $this->articleID);

            $data = $stmt->fetch($column);

            if ($data) {
                return $data;
            }
            return null;
        }
        return null;
    }

    public function allowedArticle(): bool
    {
        $stmt = $this->db->query
            ->from('allowed_articles')
            ->select('name')
            ->where('name', $this->selector->article);

        $result = $stmt->fetch('name');

        if (!$result) {
            return false;
        }
        return true;
    }

    public function exist(string $articleID): bool
    {
        if ($this->allowedArticle()) {
            $stmt = $this->db->query
                ->from('articles')
                ->select('article_id')
                ->where('article_id', $articleID);

            $result = $stmt->fetch('article_id');

            if (!$result) {
                return false;
            }
            return true;
        }
    }

    public function update(Article $article): bool
    {
        if (isset($article->articleID)) {

            $set = [
                'article_body' => $article->articleBody,
                'article_chapter' => $article->articleChapter
            ];

            $stmt = $this->db->query
                ->update('articles')
                ->set($set)
                ->where('article_id', $article->articleID)
                ->execute();

            if ($stmt) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function add(Article $article): bool
    {
        $values = [
            'article_chapter' => $article->articleChapter,
            'article_body' => $article->articleBody,
            'article_id' =>  $article->articleID
        ];

        $stmt = $this->db->query
            ->insertInto('articles')
            ->values($values)
            ->execute();

        if ($stmt) {
            return true;
        }

        return false;
    }

    public function remove(string $articleID): bool
    {
        $stmt = $this->db->query
            ->deleteFrom('articles')
            ->where('article_id', $articleID)
            ->execute();

        if ($stmt) {
            return true;
        }
        return false;
    }

}
