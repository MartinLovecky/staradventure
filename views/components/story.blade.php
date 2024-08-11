@use(Mlkali\Sa\Support\Enum)
@if(!$selector->page)
    @redirect('/storylist#storylist')
@elseif($member->permission == "visit")
    @redirect('/?message=', Enum::USER_PERMISSION)
@else
<article id="story" style="width: 80vw;">
<div class="book">
	<div class="container-story">
		<div class="text"> 
			@if (!empty($article->getArticleChapter())) 
			<h1>{{ $article->getArticleChapter() }}</h1> 
			@endif 
			@if (!empty($article->getArticleBody())) 
			@foreach (json_decode($article->getArticleBody()) as $item) 
			{!! $item !!} 
			@endforeach 
			@endif 
		</div>
	</div>
	<nav aria-label="...">
		<ul class="pagination justify-content-center">
			{!!  $pagnition->previous_page()   !!}
			{!!  $pagnition->main_pagnation()  !!}
			{!!  $pagnition->next_page()  !!}
		</ul>
	</nav>
</div>   
</article>
@endif