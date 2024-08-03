<?php

namespace Mlkali\Sa\Support;

use Exception;
use Mlkali\Sa\Engine\Blade;
use Mlkali\Sa\Support\Enum;

class Messages extends Enum
{
    public function __construct(
        public ?string $style = null,
        public ?string $message = null,
        private array $messageBag = [],
        private string $templatePath = ''
    ) {
    }

    /**
     * Retrieves all messages.
     *
     * @return array
     */
    public function getMessageBag(): array
    {
        return $this->messageBag;
    }

    /**
     * Retrieves messages of a specific type.
     *
     * @param string $type The type of messages to retrieve (success, warning, danger).
     *
     * @return array
     */
    public function getMessagesByType(string $type): array
    {
        return array_filter($this->messageBag, function ($message) use ($type) {
            return strpos($message, $type . '_') === 0;
        });
    }

    /**
     * Adds a single message or multiple messages to the message bag.
     *
     * @param string|array $message Single message as a string or multiple messages as an array.
     * @param string|null $type The type of message (success, warning, danger, etc.).
     *
     * @return self
     */
    public function addMessage(string|array $message, ?string $type = null): self
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                $this->messageBag[] = $this->formatMessage($msg, $type);
            }
        } else {
            $this->messageBag[] = $this->formatMessage($message, $type);
        }

        return $this;
    }

    /**
     * Clears the message bag.
     *
     * @return self
     */
    public function clearMessages(): self
    {
        $this->messageBag = [];
        return $this;
    }

    /**
     * Checks if there are any messages of a specific type.
     *
     * @param string $type The type of messages to check for.
     *
     * @return bool
     */
    public function hasMessagesOfType(string $type): bool
    {
        return !empty($this->getMessagesByType($type));
    }

    /**
     * Method hasAny
     *
     * @return bool
     */
    public function hasAny(): bool
    {
        return !empty($this->messageBag);
    }

    /**
     * Method getFristMessage
     *
     * @return void
     */
    public function getMessage(): void
    {
        $last = array_pop($this->messageBag);
        $exploded = explode('_', $last);
        $this->style = $exploded[0];
        $this->message = base64_encode($exploded[1]) ?? '';
    }

    /**
     * Formats a message with an optional type prefix.
     *
     * @param string $message The message to format.
     * @param string|null $type The optional message type (e.g., success, warning, danger).
     *
     * @return string
     */
    private function formatMessage(string $message, ?string $type): string
    {
        return $type ? "{$type}_{$message}" : $message;
    }
}
