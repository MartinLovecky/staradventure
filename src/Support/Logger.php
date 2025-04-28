<?php

declare(strict_types=1);

namespace Mlkali\Sa\Support;

class Logger
{
    public function logError(string $message): void
    {
        $backtrace = debug_backtrace();
        $formatted_trace = '';
        foreach ($backtrace as $trace) {
            $formatted_trace .= sprintf(
                "%s:%d %s\n",
                isset($trace['file']) ? $trace['file'] : '[unknown]',
                isset($trace['line']) ? $trace['line'] : '[unknown]',
                isset($trace['function']) ? $trace['function'] : '[unknown]'
            );
        }
        $full_message = sprintf("Error: %s\nStack Trace:\n%s\n", $message, $formatted_trace);
        error_log($full_message, 3, '/logs/php_errors.log');
    }
}
