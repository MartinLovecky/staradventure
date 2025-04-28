<?php

declare(strict_types=1);

namespace Mlkali\Sa\Html;

use Mlkali\Sa\Http\Selector;

class Pagnition
{
    private string $article = 'allwin';
    private string $type = 'story';
    private int $currentPage = 1;

    public function __construct(public Selector $selector)
    {
        //Page must be int && article Must be set
        $this->article = $this->selector->article ?? $this->article;
        $this->type = $this->getType();
        //Deafault value of page is string = we must cast to int (if string then = 0)
        $this->currentPage = ((int)$this->selector->page > 0) ? (int)$this->selector->page : $this->currentPage;
    }

    public function previous(): string
    {
        $previous = $this->currentPage - 1;
        // If page is frist there is not previous page
        if ($this->currentPage <= 1) {
            return '<li class="page-item disabled"><a class="page-link">Previous</a></li>';
        }

        return "<li class='page-item'><a class='page-link' href='" .
               "/{$this->selector->action}/{$this->article}/{$previous}#{$this->type}'>" .
               "Previous</a></li>";
    }

    public function main(): void
    {
        $range = 5;
        $totalpages = 300;
        for ($x = ($this->currentPage - $range); $x < (($this->currentPage + $range) + 1); $x++) {
            if ($x > 0 && $x <= $totalpages) {
                $active = ($this->currentPage == $x) ? 'active' : null;
                echo "<li class='page-item {$active}'><a class='page-link' href=" .
                "'/{$this->selector->action}/{$this->article}/{$x}#{$this->type}'>{$x}</a></li>";
            }
        }
    }

    public function next(): string
    {
        $next = $this->currentPage + 1;
        if ($this->currentPage >= 300) {
            return '<li class="page-item disabled"><a class="page-link">Next</a></li>';
        }
        return "<li class='page-item'><a class='page-link' href='" .
            "/{$this->selector->action}/{$this->article}/{$next}#{$this->type}'>Next</a></li>";
    }

    private function getType(): string
    {
        return match ($this->selector->action) {
            'update', 'delete', 'create' => 'edit',
            default => $this->type
        };
    }
}
