<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Repository\ArticleRepository;
use Mlkali\Sa\Support\Messages;

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
        private ArticleRepository $artRepo
    ) {
    }

    public function update(Request $request): Response
    {
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $articleID, $request->articleName, $request->articlePage));
        }

        $chapter = !empty($request->chapter) ? $request->chapter : null;
        $article_body = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"error"}';

        $this->article
            ->setArticleID($articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($article_body);

        $this->artRepo->update($this->article);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_UPDATED, $articleID));
    }

    public function create(Request $request): Response
    {
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_ALLREADY_EXIST, $articleID, $request->articleName, $request->articlePage));
        }

        $chapter = !empty($request->chapter) ? $request->chapter : null;
        $article_body = isset($request->editor1) ? json_encode(['article_body' => $request->editor1]) : '{"article_body":"empty"}';

        $this->article
            ->setArticleID($articleID)
            ->setArticleChapter($chapter)
            ->setArticleBody($article_body);

        $this->artRepo->add($this->article);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_CREATED, $articleID));
    }

    public function delete(Request $request): Response
    {
        $articleID = $request->articleName . '|' . $request->articlePage;

        if (!$this->artRepo->exist($articleID) && !$this->artRepo->allowedArticle()) {
            return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DOES_NOT_EXIST, $articleID, $request->articleName, $request->articlePage));
        }

        $this->artRepo->remove($articleID);

        return new Response("/update/$request->articleName/$request->articlePage?message=", sprintf(Messages::ARTICLE_DELETED, $articleID));
    }
}
