<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Support\Messages;

class ArticleController
{
    /**
     * ArticleController
     * - sending @param Response
     * - on /update|create|delete/articleID |
     * @return void
     */
    public function __construct(
        public Article $article,
        protected ArticleRepository $articleRepository,
    ) {
    }

    /**
     * Method update
     *
     * @param Request $request
     *
     * @return Response
     */
    public function update(Request $request): Response
    {
        $selector = $this->articleRepository->selector;
        if (!$this->articleExist()) {
            return new Response(
                "/update/{$request->articleName}/{$request->articlePage}?message=",
                sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $selector->articleID, $request->articleName, $request->articlePage)
            );
        }

        $chapter = $request->chapter ?? null;
        $articleBody = $request->editor1 ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"error"}';

        $this->createOrUpdateArticle($chapter, $articleBody);

        return new Response(
            "/update/{$request->articleName}/{$request->articlePage}?message=",
            sprintf(Messages::ARTICLE_UPDATED, $selector->articleID)
        );
    }

    /**
     * Method create
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $selector = $this->articleRepository->selector;
        if (!$this->articleExist()) {
            return new Response(
                "/update/{$request->articleName}/{$request->articlePage}?message=",
                sprintf(Messages::ARTICLE_ALREADY_EXISTS, $selector->articleID, $request->articleName, $request->articlePage)
            );
        }

        $chapter = $request->chapter ?? null;
        $articleBody = $request->editor1 ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"empty"}';

        $this->createOrUpdateArticle($chapter, $articleBody);

        return new Response(
            "/update/{$request->articleName}/{$request->articlePage}?message=",
            sprintf(Messages::ARTICLE_CREATED, $selector->articleID)
        );
    }

    /**
     * Method delete
     *
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $selector = $this->articleRepository->selector;
        if (!$this->articleExist()) {
            return new Response(
                "/update/{$request->articleName}/{$request->articlePage}?message=",
                sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $selector->articleID, $request->articleName, $request->articlePage)
            );
        }

        $this->articleRepository->remove($selector->articleID);

        return new Response(
            "/update/{$request->articleName}/{$request->articlePage}?message=",
            sprintf(Messages::ARTICLE_DELETED, $selector->articleID)
        );
    }

    /**
     * Method createOrUpdateArticle
     *
     * @param ?string $chapter
     * @param string $articleBody
     *
     * @return void
     */
    private function createOrUpdateArticle(?string $chapter, string $articleBody): void
    {
        $selector = $this->articleRepository->selector;
        $this->article
            ->setArticleID($selector->articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($articleBody);

        if ($this->articleExist()) {
            $this->articleRepository->update($this->article);
        }
        $this->articleRepository->add($this->article);
    }

    /**
     * articleExist
     * - from @param ArticleRepository
     * - checks @param Selector->articleID
     * @return bool
     */
    private function articleExist(): bool
    {
        return $this->articleRepository->exist($this->articleRepository->selector->articleID);
    }
}
