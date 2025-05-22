<?php

declare(strict_types=1);

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Http\{Request, Response};
use Mlkali\Sa\Support\{Messages, MessageFormatter};

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

    public function article(string $articleId = ''): Article
    {
        $result = $this->articleRepository->getArticle(
            column:'article_id',
            value:$articleId
        );

        if (isset($article)) {
            $this->article
                ->setArticleID($result['article_id'])
                ->setArticleBody(json_decode($result['article_body'], true));
            return $this->article;
        }
        return $this->article;
    }

    public function update(Request $request): Response
    {
        // generate redirect path
        $path = $this->path(message:self::UPDATE_PATH, request:$request);

        if (!$this->articleRepository->exist($request->articleID)) {
            $message = $this->messageFormatter->formatString(
                Messages::WARNING_NOT_EXIST,
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
                Messages::WARNING_EMPTY,
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
        $this->article
            ->setArticleID($request->articleID)
            ->setArticleBody($articleBody);
        // Update article
        $this->articleRepository->update($this->article);

        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}"
            . "#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter
            ->formatString(
                Messages::SUCCESS_UPDATED,
                [
                    $request->articleID,
                    $url
                ]
            );

        return new Response($path, $message, self::HASH);
    }

    public function create(Request $request): Response
    {
        $path = $this->path(message:self::UPDATE_PATH, request:$request);

        if ($this->articleRepository->exist($request->articleID)) {
            $message = $this->messageFormatter
                ->formatString(
                    Messages::WARNING_EXISTS,
                    [
                    $request->articleID,
                    $request->articleName,
                    $request->articlePage . self::HASH
                    ]
                );

            return new Response($path, $message, self::HASH);
        }

        $articleBody = $request->content
            ? json_encode(['article_body' => $request->content])
            : '{"article_body":"<p>dummy data</p>"}';
        // Article entity
        $this->article
            ->setArticleID($request->articleID)
            ->setArticleBody($articleBody);
        // add Article to DB
        $this->articleRepository->add($this->article);
        // redirect message
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}"
            . "#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter
            ->formatString(
                Messages::SUCCESS_CREATED,
                [
                    $request->articleID,
                    $url
                ]
            );

        return new Response($path, $message, self::HASH);
    }

    public function delete(Request $request): Response
    {
        $path = $this->path(message:self::UPDATE_PATH, request:$request);

        if (!$this->articleRepository->exist($request->articleID)) {
            $message = $this->messageFormatter
                ->formatString(
                    Messages::WARNING_NOT_EXIST,
                    [
                        $request->articleID,
                        $request->articleName,
                        $request->articlePage . self::HASH
                    ]
                );
            return new Response($path, $message, self::HASH);
        }
        // redirect message
        $url = "<a href='/show/{$request->articleName}/{$request->articlePage}"
            . "#story'>{$request->articleID}</a>";
        $message = $this->messageFormatter
            ->formatString(
                Messages::SUCCESS_DELETED,
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
