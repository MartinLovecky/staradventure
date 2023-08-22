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

        // to not need do this check for 2 folders we focus to /view/artilces 
        // everything /view/components must exit
        if ($endpoint === 'article' && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/views/articles/' . $componentName)) {
            $componentName = 'notFound';
        }

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

    /**
     *  endpoint split application into 2 parts 
     *   - frist is 'intro' that can be sum as landing page and its "elemets" inside /views/components
     *   - second is 'article' where user iteractive with (/show,/update,/delete/, /member) inside /views/articles
     *
     * @return string
     */
    private function endpoint(): string
    {
        $endpoint = match ($this->selector->action) {
            '', 'index', 'intro', 'register', 'login', 'storylist', 'vop', 'terms', 'reset', 'newpassword' => 'intro',
            default => 'article'
        };

        return $endpoint;
    }
}
