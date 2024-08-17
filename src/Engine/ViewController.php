<?php

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Engine\ViewModel;

class ViewController
{
    public function __construct(private ViewModel $viewModel)
    {
        $this->setQueryMessage();
        $this->setArticle();
    }

    public function view(): string
    {
        return $this->viewModel->render();
    }

    private function setQueryMessage(): void
    {
        $queryMessage = $this->viewModel->pagnition->selector->getQueryMessage('message');
        $messageClass = $this->viewModel->form->memberController->validator->memberRepository->messages;
        $validator = $this->viewModel->form->memberController->validator;

        if ($queryMessage && $validator->isBase64($queryMessage)) {
            $messageClass->addMessage($queryMessage);
        }
    }

    private function setArticle(): void
    {
        $article = $this->viewModel->articleController->article;
        $selector = $this->viewModel->pagnition->selector;
        match ($selector->action) {
            'create', 'update', 'delete', 'show' => $article->getArticleBody($this->viewModel->articleID),
            default => null
        };
    }
}
