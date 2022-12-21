<?php

namespace Mlkali\Sa\Http;

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
        foreach ($_POST as $key => $value)
            $this->{$key} = $value;
    }
}
