<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Database\Repository\ArticleRepository;

class Article extends ArticleRepository{

    public function __construct(
        private DB $db, 
        private Selector $selector,
        private ?string $articleChapter = null,
        private ?string $articleId = null,
        private ?array $articleBody = []
    )
    {
    }

    public function getArticleId(): ?string
    {
        $this->articleId = $this->getCurrentArticle($this->db)->get('id');

        return $this->articleId;
    }

    public function setArticleId(string $articleId): self
    {
        $this->readyToSet('id', $articleId);

        return $this;
    }

    public function getArticleChapter(): ?string
    {
        $this->articleChapter = $this->getCurrentArticle($this->db)->get('chapter');
        
        return $this->articleChapter;
    }

    public function setArticleChapter(?string $chapter = null): self
    {
        $this->readyToSet('chapter', $chapter);

        return $this;
    }

    public function getArticleBody(): ?array
    {
        $this->articleBody = $this->getCurrentArticle($this->db)->get('body');

        return $this->articleBody;
    }

    public function setArticleBody(string $body): self
    {
        $this->readyToSet('body', $body);

        return $this;
    }
}
