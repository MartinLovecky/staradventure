<?php

declare(strict_types=1);

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Engine\Blade;
use Mlkali\Sa\Http\{Form, Response, Selector};
use Mlkali\Sa\Support\MessageBag;
use Mlkali\Sa\Security\Encryption;
use Mlkali\Sa\Controllers\{ArticleController, MemberController};
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Security\Validator;

class ViewController
{
    public function __construct(
        private ArticleController $articleController,
        private Blade $blade,
        private Encryption $encryption,
        private Form $form,
        private Member $member,
        private MemberController $memberController,
        private MessageBag $messageBag,
        private Selector $selector,
        private Response $response,
        private Validator $validator,
    ) {
        $this->setQueryMessage();
    }

    public function render(): string
    {
        return $this->blade->run(view:'index', variables:$this->data());
    }

    public function delete(string $memberID)
    {
        $this->memberController->delete($memberID);
    }

    private function setQueryMessage(): void
    {
        if ($queryMessage = $this->selector->getQueryMessage('message')) {
            $this->messageBag->addMessage($queryMessage);
        }
    }

    private function action(): string
    {
        $component = match ($this->selector->action) {
            '', 'index' => 'header',
            'show' => 'story',
            'create', 'update', 'delete' => 'editor',
            default => $this->selector->action,
        };

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/views/components/{$component}.blade.php")) {
            return 'notFound';
        }

        return $component;
    }

    /**
     * We need asociate $view variables with class
     *
     * @return array
     */
    private function data(): array
    {
        return [
            'selector' => $this->selector,
            'message' => $this->messageBag,
            'component' => $this->action(),
            'title' => 'SA |' . $this->action(),
            'csrf' => $this->encryption->generateCSRF(),
            'response' => $this->response,
            'form' => $this->form,
            'encryption' => $this->encryption,
            'memberController' => $this->memberController,
            'articleController' => $this->articleController,
            'pagnition' => null,
            'validator' => $this->validator,
            'captha' => $_ENV['PUBLIC'],
            'member' => $this->member
        ];
    }
}
