<?php

namespace Mlkali\Sa\Http;

class Response
{
    public function __construct(
        private string $url = '',
        private ?string $message = null,
        private string $id = '#',
    ) {
        $this->setTargetUrl();
    }

    /**
     * This is used with @redirect in views
     *
     * @param string $url
     * @param ?string $msg
     * @param ?string $id
     *
     * @return void
     */
    public function redirect(string $url, ?string $msg = null, ?string $id = null): void
    {
        $location = $url . $this->getMessage($msg) . $id;
        header('Location:' . $location);
    }

    /**
     * used in class for redirtects
     * @example return new Response(url, optional message, #id[page to display])
     * @return void
     */
    private function setTargetUrl(): void
    {
        if (!empty($this->url)) {
            $location = $this->url . $this->getMessage($this->message) . $this->id;
            header('Location:' . $location);
        }
    }

    /**
     * Encode message we dont send any private data
     * - we could use Encryption if we want send private data
     * - if you want Decrypt them you need go in /views/includes/message.blade.php
     * @param ?string $message
     *
     * @return string
     */
    private function getMessage(?string $message = null): string|null
    {
        // $encryption = new Encryption();
        // $encryption->encrypt($message);
        return $message ? base64_encode($message) : null;
    }
}
