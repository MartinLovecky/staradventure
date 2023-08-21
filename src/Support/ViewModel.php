<?php

namespace Mlkali\Sa\Support;

use eftec\bladeone\BladeOne;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Html\Form;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Selector;

class ViewModel
{
    public function __construct(
        private BladeOne $blade,
        private Selector $selector,
        private Form $form,
        private MemberController $memberController,
        private Request $request,
        private Member $member,
        private Encryption $enc,
        private Messages $messages
    ) {
        $this->blade->setBaseUrl('public');
        $this->messages->getQueryMessage();
    }

    public function render(): string
    {
        $data = $this->setViewData();

        return $this->blade->run('index', $data);
    }

    private function setViewData(): array
    {
        $componentName = $this->commonentName();
        $endpoint = $this->endpoint();
        $title = 'SA | ' . $componentName;

        $data = match ($componentName) {
            'intro', 'storylist', 'terms', 'vop' => [],
            'login', 'register', 'reset', 'pwd' => [
                'form' => $this->form,
                'memberController' => $this->memberController,
                'request' => $this->request,
                'member' => $this->member,
                'enc' => $this->enc,
                'csrf' => $_ENV['CSRFKEY']
            ],
            // 404 will propably display some data not sure yet
            'notFound' => [],
            default => []
        };

        $baseArray = [
            'selector' => $this->selector,
            'message' => $this->messages, 
            'member' => $this->member, 
            'component' => $componentName, 
            'title' =>  $title,
            'endpoint' => $endpoint
        ];

        $merge = array_merge($baseArray, $data);

        return $merge;
    }

    private function commonentName(): string
    {
        $component = match ($this->selector->action) {
            '', 'index' => 'header',
            '404' => 'notFound',
            'update', 'delete', 'create' => 'editor',
            'newpassword' => 'pwd',
            default => $this->selector->action
        };

        return $component;
    }

    private function endpoint(): string
    {
        $endpoint = match ($this->selector->action) {
            '', 'index', 'intro', 'register', 'login' => 'intro',
            'update', 'delete', 'create', 'member', 'show', => 'article'
        };

        return $endpoint;
    }
}
