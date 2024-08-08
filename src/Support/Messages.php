<?php

namespace Mlkali\Sa\Support;

use Mlkali\Sa\Support\Enum;

class Messages extends Enum
{
    public function __construct(
        public ?string $style = null,
        public ?string $message = null,
        private array $messageBag = [],
        private string $templatePath = ''
    ) {
        $this->getMessageBag();
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
     * Adds a message to the message bag.
     *
     * @param string $message Single message as a string.
     * @param string|null $type The type of message (success, warning, danger, etc.).
     *
     * @return self
     */
    public function addMessage(string $message, ?string $type = null): self
    {
        $this->messageBag[] = $this->formatMessage($message, $type);

        return $this;
    }

    /**
     * Cheks if we have abny message in message bag
     *
     * @return bool
     */
    public function hasAny(): bool
    {
        return !empty($this->messageBag);
    }

    /**
     * Method getMessage return last message
     *
     * @return void
     */
    public function getMessage(): void
    {
        $last = base64_decode(array_pop($this->messageBag));
        $exploded = explode('_', $last);
        $this->style = $exploded[0];
        $this->message = $exploded[1] ?? '';
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
