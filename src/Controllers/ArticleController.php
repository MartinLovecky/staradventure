<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Selector;

class ArticleController
{

    public function __construct(
        private Article $article,
        private ArticleRepository $artRepo,
        private Selector $selector
    ) {
    }

    public function update(Request $request): Response
    {
        if (!$this->validateArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
        }

        $chapter = $request->chapter ?? null;
        $articleBody = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"error"}';

        $this->artRepo->createOrUpdateArticle($chapter, $articleBody);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_UPDATED, $this->selector->articleID));
    }

    public function create(Request $request): Response
    {
        if (!$this->validateArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_ALLREADY_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
        }

        $chapter = $request->chapter ?? null;
        $articleBody = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"empty"}';

        $this->createOrUpdateArticle($chapter, $articleBody);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_CREATED, $this->selector->articleID));
    }

    public function delete(Request $request): Response
    {
        if (!$this->validateArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
        }

        $this->artRepo->remove($this->selector->articleID);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DELETED, $this->selector->articleID));
    }

    private function  createOrUpdateArticle(?string $chapter, string $articleBody): void
    {
        $this->article
            ->setArticleID($this->selector->articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($articleBody);

        if($this->validateArticle())
        {
            $this->artRepo->update($this->article);
            return;
        }
        $this->artRepo->add($this->article);
    }

    private function validateArticle(): bool
    {
        return $this->artRepo->exist($this->selector->articleID);
    }
}
