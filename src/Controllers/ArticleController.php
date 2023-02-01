<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Selector;

/**
 * @param Article $article
 * @param ArticleRepository $artRepo
 * @method Response update(Request $request)
 * @method Response create(Request $request)
 * @method Response delete(Request $request)
 */
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
<<<<<<< HEAD
        if (!$this->artRepo->exist($this->selector->articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
=======
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $articleID, $request->articleName, $request->articlePage));
>>>>>>> 79c63082bcf0d2c62485e62b96d9f6bbb854e1cc
        }
        $chapter = !empty($request->chapter) ? $request->chapter : null;
        $article_body = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"error"}';

        $this->article
            ->setArticleID($this->selector->articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($article_body);

        $this->artRepo->update($this->article);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_UPDATED, $this->selector->articleID));
    }

    public function create(Request $request): Response
    {
<<<<<<< HEAD
        if (!$this->artRepo->exist($this->selector->articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_ALLREADY_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
=======
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_ALLREADY_EXIST, $articleID, $request->articleName, $request->articlePage));
>>>>>>> 79c63082bcf0d2c62485e62b96d9f6bbb854e1cc
        }

        $chapter = !empty($request->chapter) ? $request->chapter : null;
        $article_body = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"empty"}';

        $this->article
            ->setArticleID($this->selector->articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($article_body);

        $this->artRepo->add($this->article);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_CREATED, $this->selector->articleID));
    }

    public function delete(Request $request): Response
    {
<<<<<<< HEAD
        if (!$this->artRepo->exist($this->selector->articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $this->selector->articleID, $request->articleName, $request->articlePage));
=======
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $articleID, $request->articleName, $request->articlePage));
>>>>>>> 79c63082bcf0d2c62485e62b96d9f6bbb854e1cc
        }

        $this->artRepo->remove($this->selector->articleID);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DELETED, $this->selector->articleID));
    }
}
