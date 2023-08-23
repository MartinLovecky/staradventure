@if(!$selector->page)
    @component('articles.list_of_articles')@endcomponent
@else
    @component('articles.show_article')@endcomponent
@endif