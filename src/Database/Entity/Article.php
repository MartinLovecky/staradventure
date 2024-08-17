<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\ArticleRepository;

class Article
{
    public function __construct(
        private ArticleRepository $articleRepository,
        public ?string $articleBody = null,
        public ?string $articleID = null,
    ) {
    }

    public function setArticleID(string $articleID): self
    {
        $this->articleID = $articleID;

        return $this;
    }

    public function setArticleBody(string $body): self
    {
        $this->articleBody = $body;

        return $this;
    }

    public function getArticleBody(string $articleID): ?string
    {
        $articleBody = $this->articleRepository->getCurrentArticle('article_body', $articleID);
        // articleBody can be null if $articleID don't exist in database
        // we need check if ['article_body'] is set
        $this->articleBody = $articleBody['article_body'] ?? $articleBody;

        return $this->articleBody;
    }
}
