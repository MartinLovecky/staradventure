<?php

namespace Mlkali\Sa\ViewModels;

use eftec\bladeone\BladeOne;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Html\Form;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Selector;

class IndexModel
{

    public function __construct(
        private BladeOne $blade,
        private Selector $selector,
        private Messages $messages,
        private MemberController $memberController,
        private Member $member,
        private Form $form,
        private Request $request,
        private Encryption $enc,
        private string $title = 'SA | ',
    ) {
        $this->blade->setBaseUrl('public');
    }

    public function render(?string $component = null): string
    {
        $data = $this->setViewData($component);

        return $this->blade->run('index', $data);
    }

    private function setViewData(?string $component = null): array
    {

        $this->title .= $component;

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

        $baseArray = ['selector' => $this->selector, 'message' => $this->messages, 'member' => $this->member, 'component' => $component, 'title' =>  $this->title];

        $merge = array_merge($baseArray, $data);

        return $merge;
    }
}
