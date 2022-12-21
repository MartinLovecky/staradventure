<?php

namespace Mlkali\Sa\Http;

use Mlkali\Sa\Support\Encryption;

class Response
{

    /** Response message is encrypted so $msg need to be raw message
     * @param null|string $url where to redirect 
     * @param null|string $msg raw message
     * @param null|string $id used at index to identify which html id to display
     * @return void
     */
    public function __construct(
        private ?string $url = null,
        private ?string $msg = null,
        private ?string $id = null
    ) {
        $this->setTargetUrl();
    }

    private function setTargetUrl()
    {
        $location = $this->url . $this->getMessage() . $this->id;
        header('Location:' . $location);
    }

    private function getMessage()
    {
        if (isset($this->msg)) {
            $enc = new Encryption();
            return $enc->encrypt($this->msg);
        }
    }
}
