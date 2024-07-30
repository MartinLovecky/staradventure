<?php

namespace Mlkali\Sa\Support;

use Exception;
use Mlkali\Sa\Support\Enum;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;

class Messages extends Enum
{
    public function __construct(
        private Selector $selector,
        private Encryption $enc,
        public ?string $style = null,
        public ?string $message = null,
        private array $messageBag = []
    ) {
    }

    public function getMessageBag(): array
    {
        return $this->messageBag;
    }

    public function setMessageBag(string $message): self
    {
        $this->messageBag[] .= $message;
        $this->getFristMessage();

        return $this;
    }

    public function hasAny(): bool
    {
        if (!empty($this->messageBag)) {
            return true;
        }
        return false;
    }

    /**
     * After header we want display message /url?message=TEXTtoDISPLAY,
     * message should be encrypted ? maybe not -> we don't sent personal data
     *
     * @return void adds message to messageBag
     */
    public function getQueryMessage(): void
    {
        // $message can be null -> if url ?message=is_not_set
        $message = $this->selector->getQueryMessage("message");

        if ($message) {
            $this->setMessageBag($this->enc->decrypt($message));
        }
    }

    public function createEmailMessage(string $templateName, string|array $variables): string
    {

        if (!is_readable(__DIR__ . '/../../public/template/' . $templateName . '.html')) {
            throw new Exception("$templateName.html nexistuje ve složce /public/templates", 1);
        }

        $template = str_replace("\n", " ", preg_replace('/\s+/', ' ', file_get_contents(__DIR__ . '/../../public/template/' . $templateName . '.html')));

        return vsprintf($template, $variables);
    }

    public static function getEmailInfo(string $templateName, string $recipient): array
    {
        switch ($templateName) {
            case 'register':
                $info = ['subject' => 'Potvrzení registrace', 'to' => $recipient];
                break;
            case 'reset':
                $info = ['subject ' => 'Reset hesla', 'to' => $recipient];
                break;
            case 'user':
                $info = ['subject' => 'Zapomenutné username', 'to' => $recipient];
                break;
        }
        return $info;
    }

    public function main(): string
    {
        $template = preg_replace('/\s+/', ' ', file_get_contents(__DIR__ . '/../../public/template/main.html'));

        return str_replace('URL', $_SERVER['SERVER_NAME'], $template);
    }

    private function getFristMessage(): void
    {
        if ($this->hasAny()) {
            foreach ($this->getMessageBag() as $key => $value) {
                $exploded = explode('_', $value);
                $this->style = $exploded[0];
                $this->message = $exploded[1];
            }
        }
    }
}
