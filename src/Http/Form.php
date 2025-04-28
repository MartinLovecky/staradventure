<?php

declare(strict_types=1);

namespace Mlkali\Sa\Http;

use Exception;
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
                'register' => $this->handleDataProcessing('register', 'activate', 'register'),
                'login' =>   $this->memberController->proccesLogin($this->request),
                'forgotenUsername' => $this->handleDataProcessing('proccessForgottenUser', 'user', 'user'),
                'passwordResetSend' => $this->handleDataProcessing('proccessResetToken', 'reset', 'reset'),
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

    private function handleDataProcessing(
        string $processMethod,
        string $templateName = '',
        string $responseType = '',
        bool $generateEmailData = true
    ): Response {
        // Process the data using the specified method
        $cleanData = $this->memberController->$processMethod($this->request);
        $emailData = $generateEmailData ? $this->getEmailData($templateName, $cleanData) : [];
        $data = array_merge($cleanData, $emailData);
        // Return the response
        return $this->memberController->response($responseType, $data);
    }

    private function getEmailData(string $template = '', array $data = []): array
    {
        $body = $this->createEmailMessage($template, $data);
        $subject = $this->getSubject($template);
        $to = $this->request->email;

        return ['body' => $body, 'subject' => $subject, 'to' => $to];
    }

    private function createEmailMessage(string $template = '', array $data = []): string
    {
        $templateFile = "{$this->templatePath}{$template}.blade.php";

        if (!is_file($templateFile)) {
            throw new Exception("File: {$templateFile} cant be found");
        }

        return $this->blade->run('templates.' . $template, $data);
    }

    private function getSubject(string $template): string
    {
        return match ($template) {
            'reset' => 'Reset hesla',
            'activate' => 'Potvrzení registrace',
            'user' =>  'Zapomenutné username',
            default => 'No Subject'
        };
    }
}
