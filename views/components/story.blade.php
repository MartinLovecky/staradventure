<article id="story" style="width: 80vw;">
@if(!$selector->page)
    @component('articles.list_of_articles')@endcomponent
@else
    @component('articles.show_article')@endcomponent
@endif
</article>