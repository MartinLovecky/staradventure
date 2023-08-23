@use(Mlkali\Sa\Support\Enum)
@if($member->permission == "visit")
	{{	$response->redirect('/?message=', Enum::USER_PERMISSION, '#') }}
@endif
<div class="book">
	<div class="container-story">
		<div class="text"> 
			@if ($article->getArticleChapter() !== null && !empty($article->getArticleChapter())) 
			<h1>{{ $article->getArticleChapter() }}</h1> 
			@endif 
			@if ($article->getArticleBody() !== null && !empty($article->getArticleBody())) 
			@foreach (json_decode($article->getArticleBody()) as $item) 
			{!! $item !!} 
			@endforeach 
			@endif 
		</div>
	</div>
	<div class="pagination">
		{!!  $pagnition->previous_page()   !!}
		{!!  $pagnition->main_pagnation()  !!}
		{!!  $pagnition->next_page()  !!}
	</div>
</div>
