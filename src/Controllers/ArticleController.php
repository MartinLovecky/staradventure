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
    //TODO - only trusted user have access to editor if we should sanitaze Request $data anyway 
    private const UPDATE_PATH = "/update/%s/%s?message=";
    private const EDITOR_SELECTOR = '#edit';

    public function __construct(
        public Article $article,
        protected ArticleRepository $articleRepository,
        protected MessageFormatter $messageFormatter
    ) {}

    /**
     * Updates an existing article.
     *
     * @param Request $request containing article data.
     *
     * @return Response indicating the outcome of the update operation.
     */
    public function update(Request $request): Response
    {
        $articleID = $request->articleName . '|' . $request->articlePage;
        $path = $this->messageFormatter->formatMessage(self::UPDATE_PATH, [$request->articleName, $request->articlePage]);
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$articleID}</a>";
        if (!$this->articleRepository->exist($articleID)) {
            $message = $this->messageFormatter->formatMessage(
                Messages::ARTICLE_DOES_NOT_EXIST,
                [$articleID, $request->articleName, $request->articlePage]
            );

            return new Response($path, $message, self::EDITOR_SELECTOR);
        }

        //TODO: Article body must not be empty also we shoudl fix empty spaces
        $articleBody = json_encode(['article_body' => mb_convert_encoding($request->content, 'UTF-8')]);
        // Article entity
        $this->article->setArticleID($articleID)->setArticleBody($articleBody);
        // Update article
        $this->articleRepository->update($this->article);
        $message = $this->messageFormatter->formatMessage(Messages::ARTICLE_UPDATED, [$articleID, $url]);

        return new Response($path, $message, self::EDITOR_SELECTOR);
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
        $articleID = $request->articleName . '|' . $request->articlePage;
        $path = $this->messageFormatter->formatMessage(self::UPDATE_PATH, [$request->articleName, $request->articlePage]);
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$articleID}</a>";
        if ($this->articleRepository->exist($articleID)) {
            $message = $this->messageFormatter->formatMessage(
                Messages::ARTICLE_ALREADY_EXISTS,
                [$articleID, $request->articleName, $request->articlePage]
            );

            return new Response($path, $message, self::EDITOR_SELECTOR);
        }
        // Data from editor or dummy data that can be edited latter
        $articleBody = $request->content ? json_encode(['article_body' => $request->content]) : '{"article_body":"<p>dummy data</p>"}';
        // Article entity
        $this->article->setArticleID($articleID)->setArticleBody($articleBody);
        // add article to DB that can be edited
        $this->articleRepository->add($this->article);
        $message = $this->messageFormatter->formatMessage(Messages::ARTICLE_CREATED, [$articleID, $url]);

        return new Response($path, $message, self::EDITOR_SELECTOR);
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
        $articleID = $request->articleName . '|' . $request->articlePage;
        $path = $this->messageFormatter->formatMessage(self::UPDATE_PATH, [$request->articleName, $request->articlePage]);
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}#story'>{$articleID}</a>";
        if (!$this->articleRepository->exist($articleID)) {
            $message = $this->messageFormatter->formatMessage(
                Messages::ARTICLE_DOES_NOT_EXIST,
                [$articleID, $request->articleName, $request->articlePage]
            );

            return new Response($path, $message, self::EDITOR_SELECTOR);
        }
        $message = $this->messageFormatter->formatMessage(Messages::ARTICLE_DELETED, [$articleID, $url]);
        $this->articleRepository->remove($articleID);

        return new Response($path, $message, self::EDITOR_SELECTOR);
    }
}
