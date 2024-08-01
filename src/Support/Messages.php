<?php

namespace Mlkali\Sa\Support;

use Exception;
use Mlkali\Sa\Support\Enum;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;

class Messages extends Enum
{
    public function __construct(
        public Selector $selector,
        public Encryption $encryption,
        public ?string $style = null,
        public ?string $message = null,
        private array $messageBag = []
    ) {
    }

    /**
     * Method getMessageBag
     *
     * @return array
     */
    public function getMessageBag(): array
    {
        return $this->messageBag;
    }

    /**
     * Method setMessageBag
     *
     * @param string $message [explicite description]
     *
     * @return self
     */
    public function setMessageBag(string $message): self
    {
        $this->messageBag[] .= $message;
        $this->getFristMessage();

        return $this;
    }

    /**
     * Method hasAny
     *
     * @return bool
     */
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
            $this->setMessageBag($this->encryption->decrypt($message));
        }
    }

    /**
     * Method createEmailMessage
     *
     * @param string $templateName [explicite description]
     * @param string|array $variables [explicite description]
     *
     * @return string
     */
    public function createEmailMessage(string $templateName, string|array $variables): string
    {

        if (!is_readable(__DIR__ . '/../../public/template/' . $templateName . '.html')) {
            throw new Exception("$templateName.html nexistuje ve složce /public/templates", 1);
        }

        $template = str_replace("\n", " ", preg_replace('/\s+/', ' ', file_get_contents(__DIR__ . '/../../public/template/' . $templateName . '.html')));

        return vsprintf($template, $variables);
    }

    /**
     * Method getEmailInfo
     *
     * @param string $templateName [explicite description]
     * @param string $recipient [explicite description]
     *
     * @return array
     */
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

    /**
     * Method main
     *
     * @return string
     */
    public function main(): string
    {
        $template = preg_replace('/\s+/', ' ', file_get_contents(__DIR__ . '/../../public/template/main.html'));

        return str_replace('URL', $_SERVER['SERVER_NAME'], $template);
    }

    /**
     * Method getFristMessage
     *
     * @return void
     */
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
