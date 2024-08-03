<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(
        public Selector $selector,
        protected Fluent $fluent,
        protected ?string $repoID = null
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
        $stmt = $this->fluent?->query
            ?->from('articles')
            ?->select($column)
            ?->where('article_id', $this->repoID);

        return $stmt?->fetch($column);
    }

    /**
     * Method exist
     *
     * @param ?string $articleID
     *
     * @return bool
     */
    public function exist(?string $articleID = null): bool
    {
        if (!$this->allowedArticle()) {
            return false;
        }
        $stmt = $this->fluent?->query
            ?->from('articles')
            ?->select('article_id')
            ?->where('article_id', $articleID);

        $result = $stmt?->fetch('article_id');
        //if on-empty $result is string = true. If null = false.
        return (bool)$result;
    }

    /**
     * Method update
     *
     * @param Article $article
     *
     * @return bool
     */
    public function update(Article $article): bool
    {
        if (!$article->articleID) {
            return false;
        }

        $set = [
            'article_body' => $article->articleBody,
            'article_chapter' => $article->articleChapter
        ];

        return $this->fluent?->query
            ?->update('articles')
            ?->set($set)
            ?->where('article_id', $article->articleID)
            ?->execute();
    }

    /**
     * Method add
     *
     * @param Article $article
     *
     * @return bool
     */
    public function add(Article $article): bool
    {
        $values = [
            'article_chapter' => $article->articleChapter,
            'article_body' => $article->articleBody,
            'article_id' =>  $article->articleID
        ];

        return $this->fluent?->query
            ?->insertInto('articles')
            ?->values($values)
            ?->execute();
    }

    /**
     * Method remove
     *
     * @param string $articleID
     *
     * @return bool
     */
    public function remove(string $articleID): bool
    {
        return $this->fluent?->query
            ?->deleteFrom('articles')
            ?->where('article_id', $articleID)
            ?->execute();
    }

    /**
     * Method allowedArticle
     *
     * @return bool
     */
    private function allowedArticle(): bool
    {
        $stmt = $this->fluent?->query
            ?->from('allowed_articles')
            ?->select('name')
            ?->where('name', $this->selector->article);

        return (bool)$stmt->fetch('name');
    }
}
