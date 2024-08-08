<div class="container py-4 py-xl-5">
    <div class="row mb-5">
        <div class="col-md-8 col-xl-6 text-center mx-auto">
            <h2>Všechny příběhy</h2>
            <p class="w-lg-50">Tuto stránku vidíte protože nemáte zadaný příběh prosím vyberte jeden z nasledujících</p>
        </div>
    </div>
    <div class="row gy-4 row-cols-1 row-cols-md-2 row-cols-xl-3">
        @foreach ($articleController->cardItems() as $item)
        <div class="col">
            <div class="card bg-dark">
                <img class="card-img-top w-100 d-block fit-cover" style="height: 200px;" src="@asset($item['img_src'])" alt="missing asset">
                <div class="card-body p-4">
                    <a href="{!!  $item['link']  !!}">{{  $item['title']  }}</a></h4>
                    <p class="card-text">{{  $item['description']  }}</p>
                    <div class="d-flex">
                        <img class="rounded-circle flex-shrink-0 me-3 fit-cover" width="50" height="50" src="@asset($item['author_img'])" alt="author">
                        <a style="margin-top: 1vh;" href="{!!  $item['author_link']  !!}">{{  $item['author_name']  }}</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>