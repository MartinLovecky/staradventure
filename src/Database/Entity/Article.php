<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\ArticleRepository;

class Article
{
    public function __construct(
        private ArticleRepository $articleRepository,
        public ?string $articleID = null,
        public ?string $articleBody = null
    ) {
    }

    public function getArticleID(): ?string
    {
        $this->articleID = $this->articleRepository->getCurrentArticle('article_id');

        return $this->articleID;
    }

    public function setArticleID(string $articleID): self
    {
        $this->articleID = $articleID;

        return $this;
    }

    public function getArticleBody(): ?string
    {
        $this->articleBody = $this->articleRepository->getCurrentArticle('article_body');

        return $this->articleBody;
    }

    public function setArticleBody(string $body): self
    {
        $this->articleBody = $body;

        return $this;
    }
}
