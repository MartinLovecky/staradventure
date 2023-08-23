<?php

namespace Mlkali\Sa\Support;

use eftec\bladeone\BladeOne;
use Mlkali\Sa\Controllers\ArticleController;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Html\Form;
use Mlkali\Sa\Html\Pagnition;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
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
        private ArticleController $articleController,
        private Request $request,
        private Member $member,
        private Encryption $enc,
        private Messages $messages,
        private Article $article,
        private Pagnition $pagnation,
        private Response $response
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
        $endpoint = $this->endpoint();
        $componentName = $this->componentName($endpoint);
        $baseArray = $this->baseData($componentName, $endpoint);
        $commonetData = $this->componentData($componentName);

        if($endpoint == 'intro') {
            $merge = array_merge($baseArray, $commonetData);
        } else {
            $articlesData = $this->articlesData($componentName);
            $merge = array_merge($baseArray, $articlesData);
        }

        return $merge;
    }

    private function componentName(string $endpoint): string
    {

        $component = match ($this->selector->action) {
            '', 'index' => 'header',
            '404' => 'notFound',
            'update', 'delete', 'create' => 'editor',
            'newpassword' => 'pwd',
            default => $this->selector->action
        };

        if ($endpoint === 'article' && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/views/articles/' . $component)) {
            return  'notFound';
        }

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
            '', 'index', 'intro', 'register', 'login', 'storylist', 'vop', 'terms', 'reset', 'newpassword', 'updatemember', 'logout' => 'intro',
            default => 'article'
        };

        return $endpoint;
    }

    private function baseData(string $componentName, string $endpoint): array
    {
        return [
            'selector' => $this->selector,
            'message' => $this->messages,
            'member' => $this->member,
            'component' => $componentName,
            'title' =>  'SA | ' . $componentName,
            'endpoint' => $endpoint,
            'csrf' => $_ENV['CSRFKEY'],
            'response' => $this->response
        ];
    }

    private function componentData(string $componentName): array
    {
        $commonetData = match ($componentName) {
            'intro', 'storylist', 'terms', 'vop' => [],
            'login', 'register', 'reset', 'pwd' => [
                'form' => $this->form,
                'memberController' => $this->memberController,
                'request' => $this->request,
                'member' => $this->member,
                'enc' => $this->enc
            ],
            // 404 will propably display some data not sure yet
            'notFound' => [],
            default => []
        };
        return $commonetData;
    }

    private function articlesData(string $articleName): array
    {
        $articleData = match($articleName) {
            'editor' => [
                'article' => $this->article,
                'pagmation' => $this->pagnation,
                'request' => $this->request,
                'articleController' => $this->articleController,
                'form' => $this->form
            ],
            default => []
        };

        return $articleData;
    }
}
