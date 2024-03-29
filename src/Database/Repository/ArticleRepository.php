<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(
        private Selector $selector,
        private DB $db,
        private ?string $repoID = null
    ) {
        $this->repoID = ($this->selector->article && $this->selector->page) ? $this->selector->article . '|' . $this->selector->page : null;
    }

    /**
     * Can get specific column for articleID or all columns
     * @param string|null $articleID is handled by selector
     * @param string|null $column array if null, otherwise $column value
     * @return string|null
     */
    public function getCurrentArticle(?string $column = null): string|null
    {
        if (!$this->exist($this->repoID)) {
            return null;
        }
        $stmt = $this->db?->query
            ?->from('articles')
            ?->select($column)
            ?->where('article_id', $this->repoID);

        $data = $stmt?->fetch($column);

        if (!$data) {
            return null;
        }
        return $data;
    }

    public function exist(?string $articleID = null): bool
    {
        if (!$this->allowedArticle()) {
            return false;
        }
        $stmt = $this->db?->query
            ?->from('articles')
            ?->select('article_id')
            ?->where('article_id', $articleID);

        $result = $stmt?->fetch('article_id');
        //if on-empty $result is string = true. If null = false.
        return (bool)$result;
    }

    public function update(Article $article): bool
    {
        if (!isset($article->articleID)) {
            return false;
        }

        $set = [
            'article_body' => $article?->articleBody,
            'article_chapter' => $article?->articleChapter
        ];

        $stmt = $this->db?->query
            ?->update('articles')
            ?->set($set)
            ?->where('article_id', $article?->articleID)
            ?->execute();

        return $stmt;
    }

    public function add(Article $article): bool
    {
        $values = [
            'article_chapter' => $article?->articleChapter,
            'article_body' => $article?->articleBody,
            'article_id' =>  $article?->articleID
        ];

        $stmt = $this->db?->query
            ?->insertInto('articles')
            ?->values($values)
            ?->execute();

        return $stmt;
    }

    public function remove(string $articleID): bool
    {
        $stmt = $this->db?->query
            ?->deleteFrom('articles')
            ?->where('article_id', $articleID)
            ?->execute();

        return $stmt;
    }

    private function allowedArticle(): bool
    {
        $stmt = $this->db?->query
            ?->from('allowed_articles')
            ?->select('name')
            ?->where('name', $this->selector->article);

        $result = $stmt->fetch('name');

        return (bool)$result;
    }
}
