<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(public Fluent $fluent)
    {
    }

    /**
     * Retrieves the current article data based on the specified column and article ID.
     *
     * @param string $column The column to select from the `articles` table.
     * @param string $articleID The unique identifier for the article.
     *
     * @return array|null The article data as an associative array, or null if the article does not exist.
     */
    public function getCurrentArticle(string $column, string $articleID): ?array
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
     * Checks if an article with the given ID exists in the `articles` table.
     *
     * @param string $articleID The unique identifier for the article.
     *
     * @return bool true if the article exists, false otherwise.
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
     * Updates the article data in the `articles` table.
     *
     * @param Article $article
     *
     * @return bool true if the update was successful, false otherwise.
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
     * Adds a new article to the `articles` table.
     *
     * @param Article $article
     *
     * @return bool true if the article was successfully added, false otherwise.
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
     * Removes an article from the `articles` table based on the article ID.
     *
     * @param string $articleID
     *
     * @return bool true if the removal was successful, false otherwise.
     */
    public function remove(string $articleID): bool
    {
        return $this->fluent?->query
            ?->deleteFrom('articles')
            ?->where('article_id', $articleID)
            ?->execute();
    }

    /**
     * Checks if an article name is allowed by verifying it against the `allowed_articles` table.
     *
     * @param string $articleName The name of the article to check.
     *
     * @return bool True if the article name is allowed, false otherwise.
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
