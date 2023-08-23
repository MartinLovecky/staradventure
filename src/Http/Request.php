<?php

namespace Mlkali\Sa\Http;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Request
{
    public function __construct()
    {
        if(!empty($_FILES)) {
            foreach ($_FILES as $fkey => $fvalue) {
                $this->{$fkey} = $fvalue;
            }
        }

        foreach ($_POST as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
