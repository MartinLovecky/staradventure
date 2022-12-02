@if ($selector->article && $selector->page)
    @include('master.includes.show_article')
@else
    @include('master.includes.list_of_articles')    
@endif