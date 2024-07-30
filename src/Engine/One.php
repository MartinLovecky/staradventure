<?php

namespace Mlkali\Sa\Engine;

trait One
{
    public $phpTag = '<?php ';
    protected $phpTagEcho = '<?php' . ' echo ';
    protected int $matchCount = 0;
    protected bool $firstCaseInMatch = true;
    protected array $args = [];
    protected ?string $do = null;

    public function compileRedirect(string $expression)
    {
        $this->args($expression);
        dd($this->args);
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

    /**
     * start of  @match()
     *
     * @param mixed $expression 
     *
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
     * @param string $expression [explicite description]
     *
     * @return mixed
     */
    protected function compileDo(string $expression): mixed
    {
        $this->do = $expression;
        return $this->args($expression)[0] . ',';
    }

    /**
     * @case(supports multiple, values)  
     *
     * @param string $expression [explicite description]
     *
     * @return string
     */
    protected function compileCase(string $expression): string
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
     * @default($value) can be used in @match before @endmatch
     * @param string $expression [explicite description]
     *
     * @return string
     */
    protected function compileDefault($expression): string
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
