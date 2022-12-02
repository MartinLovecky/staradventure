<?php 

namespace Mlkali\Sa\Html;

use Mlkali\Sa\Support\Selector;

class Pagnition{

    public function __construct(
        private Selector $selector,
        private string $article = 'allwin',
        private int $page = 1
    )
    {
        //Page must be int && article Must be set, viewName is allways set
        //Deafault value of page is string = we must cast to int (if string then = 0)
        $this->article = (isset($this->selector->article)) ? $this->selector->article : $this->article;
        $this->page = ((int)$this->selector->page > 0) ? (int)$this->selector->page : $this->page;
    }

    public function previous_page()
    {
        $previous_page =  $this->page - 1;
        // If page is frist or above limit(should not haappen)  there is not previous page
        if($this->selector->page <= 1 || $this->selector->page >= 300){
            return '<li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
        }
        return '<li class="page-item"><a class="page-link" href="/'.$this->selector->action.'/'.$this->article.'/'.$previous_page.'#wp-pagnation" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
    }

    public function main_pagnation()
    {
        $range = 5;
        $totalpages = 300;
        for ($x = ($this->page - $range); $x < (($this->page + $range) + 1); $x++) {
            if (($x > 0) && ($x <= $totalpages)) {
                $active = ($this->page == $x) ? 'active': null;
                echo '<li class="page-item '.$active.' "><a class="page-link" href="/'.$this->selector->action.'/'.$this->article.'/'.$x.'#wp-pagnation">'.$x.'</a></li>';
            }
        }
    }

    public function next_page()
    {
        $next = $this->page + 1;
        if ($this->page < 1 || $this->page >= 300) {
            return '<li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">»</span></a></li>';
        }
        return '<li class="page-item"><a class="page-link" href="/'.$this->selector->action.'/'.$this->article.'/'.$next.'#wp-pagnation" aria-label="Previous"><span aria-hidden="true">»</span></a></li>';
    }
}