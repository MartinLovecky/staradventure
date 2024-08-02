<?php

namespace Mlkali\Sa\Engine;

use Envms\FluentPDO\Queries\Select;
use Mlkali\Sa\Controllers\ArticleController;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Database\Entity\Article;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Html\Form;
use Mlkali\Sa\Html\Pagnition;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Support\Messages;

class ViewModel
{
    public function __construct(
        protected Pagnition $pagnition,
        protected Form $form,
        protected Response $response,
        protected ?string $queryMessage = null
    ) {
        $this->$queryMessage = $this->pagnition->selector->getQueryMessage("message");
        //$this->messages->add();
    }

    public function render(): string
    {
        $data = $this->setViewData();

        return $this->form->blade->run('index', $data);
    }

    private function setViewData(): array
    {
        $endpoint = $this->endpoint();
        $componentName = $this->componentName($endpoint);
        $baseArray = $this->baseData($componentName, $endpoint);
        $commonetData = $this->componentData($componentName);

        if ($endpoint == 'intro') {
            $merge = array_merge($baseArray, $commonetData);
        } else {
            $articlesData = $this->articlesData($componentName);
            $merge = array_merge($baseArray, $articlesData);
        }

        return $merge;
    }

    private function componentName(string $endpoint): string
    {
        $component = match ($this->pagnition->selector->action) {
            '', 'index' => 'header',
            '404' => 'notFound',
            'update', 'delete', 'create' => 'editor',
            'newpassword' => 'pwd',
            'show' => 'story',
            default => $this->pagnition->selector->action
        };

        if ($endpoint === 'article' && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/views/articles/' . $component . '.blade.php')) {
            return 'notFound';
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
        $endpoint = match ($this->pagnition->selector->action) {
            '', 'index', 'intro', 'register', 'login', 'storylist', 'vop', 'terms', 'reset', 'newpassword', 'updatemember', 'logout', 'activate' => 'intro',
            default => 'article'
        };

        return $endpoint;
    }

    private function baseData(string $componentName, string $endpoint): array
    {
        return [
            'selector' => $this->pagnition->selector,
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
                'member' => $this->member,
                'encryption' => $this->enc
            ],
            'logout', 'activate' => [$this->memberController],
            // 404 will propably display some data not sure yet
            'notFound' => [],
            default => []
        };
        return $commonetData;
    }

    private function articlesData(string $articleName): array
    {
        $articleData = match ($articleName) {
            'editor' => [
                'article' => $this->article,
                'pagnition' => $this->pagnition,
                'articleController' => $this->articleController,
                'form' => $this->form
            ],
            'story' => [
                'article' => $this->article,
                'pagnition' => $this->pagnition
            ],
            default => []
        };

        return $articleData;
    }
}
