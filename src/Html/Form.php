<?php

namespace Mlkali\Sa\Html;

use eftec\bladeone\BladeOne;

class Form
{

    public function __construct(
        private BladeOne $blade,
        private string $class = 'text-center',
        private string $method = 'POST',
        private ?string $target = null,
        private string $id = 'contact-form',
        private array $values = [],
        private string $autocomplete = 'off',
        private string $enctype = 'url-encoded'
    ) {
    }

    public function options(array $options): self
    {
        $this->class = $options['class'] ?? $this->class;
        $this->method = $options['method'] ?? $this->method;
        $this->target = $options['target'] ?? $this->target;
        $this->autocomplete = $options['autocomplete'] ?? $this->autocomplete;
        $this->enctype = $options['enctype'] ?? $this->enctype;
        $this->id = $options['id'] ?? $this->id;
        return $this;
    }

    public function vars(array $values): self
    {
        $this->values = !empty($values) ? $values : $this->values;
        return $this;
    }

    public function run(): ?string
    {
        if (!empty($this->target)) {
            return "<form method='$this->method' target='" . $this->blade->run($this->target, $this->values) . "' class='$this->class' id='$this->id' autocomplete='$this->autocomplete' enctype='$this->enctype'>";
        }
        return null;
    }
}
