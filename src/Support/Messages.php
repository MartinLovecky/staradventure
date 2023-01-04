<?php

namespace Mlkali\Sa\Support;

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

    /**
     * check if messageBag has any messages
     *
     * @return bool true if message bag has any messages
     */
    public function hasAny(): bool
    {
        if (!empty($this->messageBag)) {
            return true;
        }
        return false;
    }

    /**
     * After header we want display message /url?message=TEXTtoDISPLAY, ?message is important to message work also message should be
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

    public static function createEmailMessage(string $templateType, array $placeholderValues): string
    {
        switch ($templateType) {
            case 'register':
                $template = self::EMAIL_TEMPLATE_REGISTER;
                break;
            case 'reset':
                $template = self::EMAIL_TEMPLATE_RESET;
                break;
            case 'user':
                $template = self::EMAIL_TEMPLATE_USER;
                break;
            case 'main':
                $template = self::MAIN_EMAIL_TEMPLATE;
                break;
        }
        return vsprintf($template, $placeholderValues);
    }

    public static function getEmailInfo(string $templateType, string $recipient): array
    {
        switch ($templateType) {
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
     * Gets frist message
     *
     * @return void sets style and message
     */
    private function getFristMessage(): void
    {
        if ($this->hasAny()) {

            foreach ($this->getMessageBag() as $key => $value) {
                $exploded = explode('.', $value);
                $this->style = $exploded[0];
                $this->message = $exploded[1];
            }
        }
    }
}
