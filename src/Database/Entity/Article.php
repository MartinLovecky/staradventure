<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Entity;

class Article
{
    private ?string $articleBody = null;
    private ?string $articleID = null;

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

    public function getArticleID(): ?string
    {
        return $this->articleID;
    }

    public function getArticleBody(): ?string
    {
        return $this->articleBody;
    }
}
