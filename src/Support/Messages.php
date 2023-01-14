<?php

namespace Mlkali\Sa\Support;

use voku\helper\HtmlMin;
use Mlkali\Sa\Support\Enum;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;

/**
 * @param Selector $selector
 * @param Encryption $enc
 * @param HtmlMin $htmlMin
 * @param null|string $style
 * @param null|string $message
 */
class Messages extends Enum
{

    public function __construct(
        private Selector $selector,
        private Encryption $enc,
        private HtmlMin $htmlMin,
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

    public function createEmailMessage(string $templateType, array $placeholderValues): string
    {
        if ($templateType === 'main') {

            $template = $this->htmlMin->minify(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/main.html'));
            
            return str_replace(['URL', "\n"], [$placeholderValues[0], " "], $template);
        }

        $template = str_replace(
            "\n",
            " ",
           $this->htmlMin->minify(
                file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/' . $templateType . '.html')
            )
        );

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
