<?php

declare(strict_types=1);

namespace Mlkali\Sa\Http;

use Mlkali\Sa\Engine\Blade;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Controllers\{ArticleController, MemberController};

class Form
{
    private string $class = 'text-center';
    private string $method = 'POST';
    private string $target = 'requestHandler';
    private string $id = 'contact-form';
    private string $autocomplete = 'off';
    private string $enctype = 'application/x-www-form-urlencoded';
    private string $templatePath = '';

    public function __construct(
        private Blade $blade,
        private Request $request,
        private ArticleController $articleController,
        private MemberController $memberController,
    ) {
        $this->templatePath = dirname(__DIR__, 2) . "/views/templates/";
    }

    public function options(array $options = []): self
    {
        $this->class = $options['class'] ?? $this->class;
        $this->method = $options['method'] ?? $this->method;
        $this->target = $options['target'] ?? $this->target;
        $this->autocomplete = $options['autocomplete'] ?? $this->autocomplete;
        $this->enctype = $options['enctype'] ?? $this->enctype;
        $this->id = $options['id'] ?? $this->id;
        return $this;
    }

    public function run(array $options = []): mixed
    {
        if (!empty($options)) {
            $this->options($options);
        }

        if ($_POST) {
            match ($this->request->type) {
                'register' => $this->memberController->register($this->request),
                'login' => $this->memberController->login($this->request),
                'forgoten' => $this->memberController->forgoten($this->request),
                'new_password' => $this->memberController->setNewPassword($this->request),
                'update_member' => $this->memberController->updateMember($this->request),
                'update' => $this->articleController->update($this->request),
                'create' => $this->articleController->create($this->request),
                'delete' => $this->articleController->delete($this->request),
                default => null
            };
        }
        return "<form method='{$this->method}' target='_self' class='{$this->class}'"
            . "id='{$this->id}' autocomplete='{$this->autocomplete}' enctype='{$this->enctype}'>";
    }
}
