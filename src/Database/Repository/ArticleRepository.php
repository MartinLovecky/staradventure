<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(
        public Selector $selector,
        public Fluent $fluent,
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
     * Checks if an article with the given ID exists in the repository.
     *
     * @param string $articleID The unique identifier for the article.
     *
     * @return bool True if the article exists, false otherwise.
     */
    public function exist(string $articleID): bool
    {
        $articleName = explode('|', $articleID)[0];
        if (!$this->allowedArticle($articleName)) {
            return false;
        }
        return (bool)$this->fluent?->query
            ?->from('articles')
            ?->select('article_id')
            ?->where('article_id', $articleID)
            ?->fetch('article_id');
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
        $set = ['article_body' => $article->articleBody];

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
     * if name is in DB it returns string 
     * that why we need to cast it to bool
     * -  if not default is false
     * @return bool
     */
    private function allowedArticle($articleName): bool
    {
        return (bool)$this->fluent?->query
            ?->from('allowed_articles')
            ?->select('name')
            ?->where('name', $articleName)
            ?->fetch('name');
    }
}
