<?php

namespace Mlkali\Sa\Http;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Request
{

    /**
     * Convert $_POST array to public properties 
     * where array key is property name
     * @return object
     */
    public function getRequest()
    {
        foreach($_FILES as $fkey => $fvalue)
        {
            $this->{$fkey} = $fvalue;
        }

        foreach ($_POST as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
