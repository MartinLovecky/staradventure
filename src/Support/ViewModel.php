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
    }

    public function render(): string 
    {
        $data = $this->setViewData();

        return $this->blade->run('index', $data);
    }

    private function setViewData(): array
    {
        $component = $this->commonentName();
        $title = 'SA | ' . $component;

        $data = match ($component) {
            'intro', 'storylist', 'terms', 'vop' => [],
            'login', 'register', 'reset' => [
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

        $baseArray = ['selector' => $this->selector, 'message' => $this->messages, 'member' => $this->member, 'component' => $component, 'title' =>  $title];

        $merge = array_merge($baseArray, $data);

        return $merge;
    }

    private function commonentName(): string
    {
        $view = match($this->selector->action)
        {
            '' => 'index',
            '404' => 'notFound',
            default => $this->selector->action
        };

        return $view;
    }
}
