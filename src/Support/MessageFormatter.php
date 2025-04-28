<?php

declare(strict_types=1);

namespace Mlkali\Sa\Support;

class MessageFormatter
{
    /**
     * Formats a message with the given data.
     *
     * @param string $message The message template with placeholders (e.g., "Hello, %s").
     * @param array $data An array of values to replace the placeholders in the message.
     *
     * @return string The formatted message.
     */
    public function formatString(string $message, array $data): string
    {
        return sprintf($message, ...$data);
    }
}
