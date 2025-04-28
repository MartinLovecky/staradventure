<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Database\Entity\Article;

class ArticleRepository
{
    public function __construct(private Fluent $fluent)
    {
    }

    /**
     *
     * @param string|null $column
     * @param string|null $value
     * @param string|null $item
     * @return array
     */
    public function getArticle(
        ?string $column = null,
        ?string $value = null,
        ?string $item = null
    ): array {
        $result = $this->fluent->query
            ->from('articles')
            ->where($column, $value)
            ->fetch($item);
        if (!$result) {
            return [];
        } elseif (!is_array($result)) {
            return [$result];
        }

        return $result;
    }

    /**
     * Checks if an article with the given ID exists in the `articles` table.
     *
     * @param string $articleID The unique identifier for the article.
     *
     * @return bool true if the article exists, false otherwise.
     */
    public function exist(string $articleID = ''): bool
    {
        return $this->fluent->query
            ->from('articles')
            ->select('article_id')
            ->where('article_id', $articleID)
            ->fetch('article_id') == ! false;
    }

    /**
     * Updates the article data in the `articles` table.
     *
     * @param Article $article
     *
     * @return int|false int update done, false otherwise.
     */
    public function update(Article $article): int|false
    {
        return $this->fluent->query
            ->update('articles')
            ->set(['article_body' => $article->getArticleBody()])
            ->where('article_id', $article->getArticleID())
            ->execute();
    }

    /**
     * Adds a new article to the `articles` table.
     *
     * @param Article $article
     *
     * @return int|false int article added, false otherwise.
     */
    public function add(Article $article): int|false
    {
        return $this->fluent->query
            ->insertInto('articles')
            ->values([
                'article_body' => $article->getArticleBody(),
                'article_id' => $article->getArticleID()
            ])
            ->execute();
    }

    /**
     * Removes an article from the `articles` table based on the article ID.
     *
     * @param string $articleID
     *
     * @return int|false int on removal, false otherwise.
     */
    public function remove(string $articleID): int|false
    {
        return $this->fluent->query
            ->deleteFrom('articles')
            ->where('article_id', $articleID)
            ->execute();
    }
}
