<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\ArticleRepository;

class Article extends ArticleRepository{

    public function __construct( 
        private ?string $articleChapter = null,
        private ?string $articleId = null,
        private ?string $articleBody = ''
    )
    {
    }

    public function getArticleId(): ?string
    {
        $this->articleId = $this->getCurrentArticle()->get('id');

        return $this->articleId;
    }

    public function setArticleId(string $articleId): self
    {
        $this->readyToSet('id', $articleId);

        return $this;
    }

    public function getArticleChapter(): ?string
    {
        $this->articleChapter = $this->getCurrentArticle()->get('chapter');
        
        return $this->articleChapter;
    }

    public function setArticleChapter(?string $chapter = null): self
    {
        $this->readyToSet('chapter', $chapter);

        return $this;
    }

    public function getArticleBody(): ?string
    {
        $this->articleBody = $this->getCurrentArticle()->get('body');

        return $this->articleBody;
    }

    public function setArticleBody(string $body): self
    {
        $this->readyToSet('body', $body);

        return $this;
    }
}
