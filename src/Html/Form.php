<?php

namespace Mlkali\Sa\Html;

use Exception;
use Mlkali\Sa\Engine\Blade;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Controllers\ArticleController;
use Mlkali\Sa\Http\Response;

class Form
{
    public function __construct(
        public Blade $blade,
        public Request $request,
        public ArticleController $articleController,
        public MemberController $memberController,
        private string $class = 'text-center',
        private string $method = 'POST',
        private string $target = 'requestHandler',
        private string $id = 'contact-form',
        private string $autocomplete = 'off',
        private string $enctype = 'application/x-www-form-urlencoded',
        private string $templatePath = '',
        protected string $url = ''
    ) {
        $this->templatePath = dirname(__DIR__, 2) . "/views/templates/";
        $this->url = $_SERVER['SERVER_NAME'] ?? 'localhost';
    }

    public function options(array $options): self
    {
        $this->class = $options['class'] ?? $this->class;
        $this->method = $options['method'] ?? $this->method;
        $this->target = $options['target'] ?? $this->target;
        $this->autocomplete = $options['autocomplete'] ?? $this->autocomplete;
        $this->enctype = $options['enctype'] ?? $this->enctype;
        $this->id = $options['id'] ?? $this->id;
        return $this;
    }

    public function run(?array $options = null)
    {
        if (isset($options)) {
            $this->options($options);
        }

        if ($_POST) {
            match ($this->request->type) {
                'register' => $this->handleDataProcessing('proccesRegister', 'activate', 'register'),
                'reset_send' => $this->handleDataProcessing('proccessResetToken', 'reset', 'reset'),
                'reset_user' => $this->handleDataProcessing('proccessForgottenUser', 'user', 'user'),
                'login' =>   $this->memberController->proccesLogin($this->request),
                'new_password' => $this->memberController->setNewPassword($this->request),
                'updateMember' => $this->memberController->updateMember($this->request),
                'update' => $this->articleController->update($this->request),
                'create' => $this->articleController->create($this->request),
                'delete' => $this->articleController->delete($this->request)
            };
        }
        return "<form method='{$this->method}' target='_self' class='{$this->class}' id='{$this->id}' autocomplete='{$this->autocomplete}' enctype='{$this->enctype}'>";
    }

    /**
     * Handles the common workflow of processing data, generating email data, and returning a response.
     *
     * @param string $processMethod The name of the memberController method for processing data.
     * @param string $templateName The name of the email template to use.
     * @param string $responseType The type of response to return.
     * @param bool $generateEmailData Whether to generate email data or not.
     *
     * @return Response The response object.
     */
    private function handleDataProcessing(
        string $processMethod,
        string $templateName = '',
        string $responseType = '',
        bool $generateEmailData = true
    ): Response {
        // Process the data using the specified method
        $cleanData = $this->memberController->$processMethod($this->request);

        // If needed, generate email data
        $emailData = $generateEmailData ? $this->getEmailData($templateName, $cleanData) : $cleanData;

        // Return the response
        return $this->memberController->response($responseType, $emailData);
    }

    /**
     * Collects email data including body, subject, and recipient email address.
     *
     * @param string $templateName The name of the email template file (without extension).
     * @param array $data An associative array of data to be passed to the template for rendering.
     *
     * @return array An associative array containing:
     *               - 'body' (string): The rendered email body.
     *               - 'subject' (string): The email subject line.
     *               - 'to' (string): The recipient's email address.
     */
    private function getEmailData(string $templateName, array $data): array
    {
        $body = $this->createEmailMessage($templateName, $data);
        $subject = $this->getSubject($templateName);
        $to = $this->request->email;

        return ['body' => $body, 'subject' => $subject, 'to' => $to];
    }

    /**
     * Generates the email body by rendering the specified template with the provided data.
     *
     * @param string $templateName The name of the template file (without extension).
     * @param array $data An associative array of data to be used within the template.
     *
     * @return string The rendered email content.
     *
     * @throws Exception If the template file is not readable or does not exist.
     */
    private function createEmailMessage(string $templateName, array $data): string
    {
        $templateFile = $this->templatePath . $templateName . '.blade.php';

        if (!is_file($templateFile)) {
            throw new Exception("File: {$templateFile} cant be found");
        }

        return $this->blade->run('templates.' . $templateName, $data);
    }

    /**
     * Returns the subject line for the email based on the template name.
     *
     * @param string $templateName The name of the email template.
     *
     * @return string The subject line for the email.
     */
    private function getSubject(string $templateName): string
    {
        return match ($templateName) {
            'reset' => 'Reset hesla',
            'activate' => 'Potvrzení registrace',
            'user' =>  'Zapomenutné username',
            default => 'No Subject'
        };
    }
}
