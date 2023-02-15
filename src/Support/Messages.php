<?php

namespace Mlkali\Sa\Support;

use Exception;


use Mlkali\Sa\Support\Enum;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;

/**
 * @param Selector $selector
 * @param Encryption $enc
 * @param null|string $style
 * @param null|string $message
 */
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
     * ?message is important to message work also message should be
     * encrypted, also dont use ?message if you dont provide encrypted message 
     *
     * @return void adds message to messageBag
     */
    public function getQueryMessage(): void
    {
        if (isset($this->selector->queryMsg) && $this->enc->decrypt($this->selector->queryMsg) !== '') {
            $this->setMessageBag($this->enc->decrypt($this->selector->queryMsg));
        }
    }

    public function createEmailMessage(string $templateName, string|array $variables): string
    {
        if (!is_readable(__DIR__ . '/../../public/template/' . $templateName . '.html')) {
            throw new Exception("$templateName.html nexistuje ve složce /public/templates", 1);
        }

        if ($templateName === 'main' && is_string($variables)) {

            $template = preg_replace( '/\s+/', ' ', file_get_contents(__DIR__ . '/../../public/template/' . $templateName . '.html'));

            return str_replace(['URL', "\n"], [$variables, " "], $template);
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
