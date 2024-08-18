<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Support\MessageFormatter;
use Mlkali\Sa\Support\Messages;

/**
 * Class ArticleController
 *
 * This controller handles the creation, updating, and deletion of articles.
 * It interacts with the Article entity and the ArticleRepository to manage
 * article data.
 *
 * @package Mlkali\Sa\Controllers
 */
class ArticleController
{
    private const UPDATE_PATH = "/update/%s/%s?message=";
    private const HASH = '#edit';

    public function __construct(
        public Article $article,
        protected ArticleRepository $articleRepository,
        protected MessageFormatter $messageFormatter
    ) {
    }

    /**
     * Updates an existing article.
     *
     * @param Request $request containing article data.
     *
     * @return Response indicating the outcome of the update operation.
     */
    public function update(Request $request): Response
    {
        // generate redirect path
        $path = $this->path(self::UPDATE_PATH, $request);

        if (!$this->articleRepository->exist($request->articleID)) {
            $message = $this->messageFormatter->formatString(
                Messages::ARTICLE_DOES_NOT_EXIST,
                [
                    $request->articleID,
                    $request->articleName,
                    $request->articlePage . self::HASH
                ]
            );

            return new Response($path, $message, self::HASH);
        }
        if (empty($request->content)) {
            $message = $this->messageFormatter->formatString(
                Messages::EMPTY_ARTICLE,
                [
                    $request->articleID,
                    $request->articleName,
                    $request->articlePage . self::HASH
                ]
            );
            return new Response($path, $message, self::HASH);
        }

        $articleBody = json_encode(['article_body' => mb_convert_encoding($request->content, 'UTF-8')]);
        // Article entity
        $this->article->setArticleID($request->articleID)->setArticleBody($articleBody);
        // Update article
        $this->articleRepository->update($this->article);

        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter->formatString(
            Messages::ARTICLE_UPDATED,
            [
                $request->articleID,
                $url
            ]
        );

        return new Response($path, $message, self::HASH);
    }

    /**
     * Creates a new article.
     *
     * @param Request $request containing article data.
     *
     * @return Response indicating the outcome of the creation operation.
     */
    public function create(Request $request): Response
    {
        // generate redirect path
        $path = $this->path(self::UPDATE_PATH, $request);

        if ($this->articleRepository->exist($request->articleID)) {
            $message = $this->messageFormatter->formatString(
                Messages::ARTICLE_ALREADY_EXISTS,
                [
                    $request->articleID,
                    $request->articleName,
                    $request->articlePage . self::HASH
                ]
            );

            return new Response($path, $message, self::HASH);
        }
        // Data from editor or dummy data that can be edited latter
        $articleBody = $request->content ? json_encode(['article_body' => $request->content]) : '{"article_body":"<p>dummy data</p>"}';
        // Article entity
        $this->article->setArticleID($request->articleID)->setArticleBody($articleBody);
        // add Article to DB that can be edited
        $this->articleRepository->add($this->article);
        // redirect message
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter->formatString(
            Messages::ARTICLE_CREATED,
            [
                $request->articleID,
                $url
            ]
        );

        return new Response($path, $message, self::HASH);
    }

    /**
     * Deletes an existing article.
     *
     * @param Request $request identifying the article to be deleted.
     *
     * @return Response indicating the outcome of the deletion operation.
     */
    public function delete(Request $request): Response
    {
        // generate redirect path
        $path = $this->path(self::UPDATE_PATH, $request);

        if (!$this->articleRepository->exist($request->articleID)) {
            // generate error message for redirect
            $message = $this->messageFormatter->formatString(
                Messages::ARTICLE_DOES_NOT_EXIST,
                [
                    $request->articleID,
                    $request->articleName,
                    $request->articlePage . self::HASH
                ]
            );

            return new Response($path, $message, self::HASH);
        }
        // redirect message
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter->formatString(
            Messages::ARTICLE_DELETED,
            [
                $request->articleID,
                $url
            ]
        );
        $this->articleRepository->remove($request->articleID);

        return new Response($path, $message, self::HASH);
    }

    private function path(string $message, Request $request): string
    {
        $array = [$request->articleName, $request->articlePage];

        return $this->messageFormatter->formatString($message, $array);
    }
}
