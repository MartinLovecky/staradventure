<?php

namespace Mlkali\Sa\Engine;

trait One
{

    protected int $matchCount = 0;
    protected bool $firstCaseInMatch = true;
    protected array $args = [];
    protected ?string $do = null;

    /**
     * used in views via @redirect('view', 'optional message')
     *
     * @param string $expression 
     *
     * @return string
     */
    public function compileRedirect(string $expression): string
    {
        return $this->phpTagEcho . "\$response->redirect{$expression};?>";
    }

    /**
     * @form([options]) default are used when array not provided
     * - check \Mlkali\Sa\Html\Form options
     * @param string $expression
     *
     * @return void
     */
    public function compileForm(string $expression): string
    {
        return $this->phpTag . "echo \$form->run{$expression};?>";
    }

    /**
     * IF we need convert $expression to array
     * @param string $expression
     * @return array $args[0] , $args[1] ...
     */
    protected function args(string $expression): array
    {
        // Remove the surrounding parentheses and split by comma
        $expression = trim($expression, '()');
        $parts = explode(',', $expression);

        foreach ($parts as $index => $part) {
            // Remove surrounding whitespace and quotes
            $part = trim($part, " \t\n\r\0\x0B'\"");
            $part = is_numeric($part) ? (int)$part : $part;
            // Add the part to the args array
            $this->args[(int)$index] = $part;
        }

        return $this->args;
    }

    public function compileDe($expression): string
    {
        return $this->phpTag . "\dd{$expression};?>";
    }

    /**
     * start of  @match()
     * - preset is optional for default value
     * @param mixed $expression
     * @example - @match(x) @state('this')@do(1..)@preset()@endmatch()
     * @return string
     */
    protected function compileMatch($expression): string
    {
        $this->matchCount++;
        $this->firstCaseInMatch = true;
        return $this->phpTag . "match $expression {";
    }

    /**
     * @do(action that should happen)
     * @example usage: @case(1) @do()
     * @param string $expression
     *
     * @return mixed
     */
    protected function compileDo(string $expression): mixed
    {
        $this->do = $expression;
        return $this->args($expression)[0] . ',';
    }

    /**
     * @state(supports multiple, values)
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileState(string $expression): string
    {
        $args = $this->args($expression);
        $caseStrings = [];
        foreach ($args as $arg) {
            // Create a mapping for each case argument
            $caseStrings[] = var_export($arg, true);
        }
        $compiledCases = implode(", ", $caseStrings);
        $result = "$compiledCases => $this->do";

        if ($this->firstCaseInMatch) {
            $this->firstCaseInMatch = false;
        }

        return $result;
    }

    /**
     * @preset($value) can be used in @match before @endmatch
     * @param string $expression
     *
     * @return string
     */
    protected function compilePreset($expression): string
    {
        if ($this->firstCaseInMatch) {
            return $this->showError('@default', '@match without any @case', true);
        }
        if ($expression) {
            return 'default => ' . $this->args($expression)[0] . ',';
        }
    }

    /**
     * @match need have @endmatch
     *
     * @return string
     */
    protected function compileEndMatch(): string
    {
        --$this->matchCount;
        if ($this->matchCount < 0) {
            return $this->showError('@endmatch', 'Missing @match', true);
        }
        return '};?>';
    }
}
