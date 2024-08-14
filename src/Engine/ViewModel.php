<?php

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Html\Pagnition;
use Mlkali\Sa\Html\Form;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Controllers\ArticleController;

class ViewModel
{
    public function __construct(
        protected Pagnition $pagnition,
        protected Form $form,
        protected Response $response,
        protected ArticleController $articleController,
    ) {
        $this->setQueryMessage();
        $this->userData();
    }

    public function render(): string
    {
        $data = $this->getViewData();

        return $this->form->blade->run('index', $data);
    }

    private function getViewData(): array
    {
        $componentName = $this->componentName();
        $baseArray = $this->baseData($componentName);
        $commonetData = $this->componentData($componentName);

        return array_merge($baseArray, $commonetData);
    }

    /**
     * changes the name of the file we want to run from /action in views/componets,
     * or assigns multiple actions to a single view.
     * @return string
     */
    private function componentName(): string
    {
        $component = match ($this->pagnition->selector->action) {
            '', 'index' => 'header',
            '404' => 'notFound',
            'update', 'delete', 'create' => 'editor',
            'show' => 'story',
            default => $this->pagnition->selector->action
        };

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/views/components/' . $component . '.blade.php')) {
            return 'notFound';
        }

        return $component;
    }

    /**
     * Variables needed for almost every view
     *
     * @param string $componentName
     *
     * @return array
     */
    private function baseData(string $componentName): array
    {
        return [
            'selector' => $this->pagnition->selector,
            'message' => $this->form->memberController->validator->memberRepository->messages,
            'member' => $this->form->memberController->member,
            'component' => $componentName,
            'title' =>  'SA | ' . $componentName,
            'csrf' => $_ENV['CSRFKEY'],
            'response' => $this->response,
        ];
    }

    /**
     * Variables needed for specific view
     *
     * @param string $componentName
     *
     * @return array
     */
    private function componentData(string $componentName): array
    {
        $commonetData = match ($componentName) {
            'login', 'register', 'resetPassword', 'updatemember', 'resetUsername' => [
                'form' => $this->form,
                'encryption' => $this->form->memberController->validator->encryption
            ],
            'logout' => ['memberController' => $this->form->memberController],
            'activate' => [
                'memberController' => $this->form->memberController,
                'id' => $this->pagnition->selector->getQueryMessage("id"),
                'token' => $this->pagnition->selector->getQueryMessage("token")
            ],
            'editor' => [
                'articleController' => $this->articleController,
                'article' => $this->articleController->article,
                'pagnition' => $this->pagnition,
                'form' => $this->form
            ],
            'story' => [
                'articleController' => $this->articleController,
                'article' => $this->articleController->article,
                'pagnition' => $this->pagnition
            ],
            'newpassword' => [
                'form' => $this->form,
                'encryption' => $this->form->memberController->validator->encryption,
                'memberID' => $this->pagnition->selector->getQueryMessage("id")
            ],
            default => []
        };

        return $commonetData;
    }

    private function setQueryMessage(): void
    {
        $queryMessage = $this->pagnition->selector->getQueryMessage('message');
        $messageClass = $this->form->memberController->validator->memberRepository->messages;
        $validator = $this->form->memberController->validator;

        if ($queryMessage && $validator->isBase64($queryMessage)) {
            $messageClass->addMessage($queryMessage);
        }
    }

    private function userData(): void
    {
        if (!isset($_SESSION['member_id'])) {
            $this->form->memberController->setMember('visitor');
        } elseif (isset($_SESSION['member_id'])) {
            $username = explode('|', $_SESSION['member_id'])[0];
            $this->form->memberController->setMember($username);
        }
    }
}
