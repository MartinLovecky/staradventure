<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(public Fluent $fluent) {}

    public function getCurrentArticle(string $column, string $articleID)
    {
        if (!$this->exist($articleID)) {
            return null;
        }
        $stmt = $this->fluent?->query
            ?->from('articles')
            ?->select($column)
            ?->where('article_id', $articleID)
            ?->fetch($column);
        if ($stmt) {
            return json_decode($stmt, true);
        }
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
