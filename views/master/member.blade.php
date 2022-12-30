@use(Mlkali\Sa\Http\Response)
@if(!isset($selector->article))
    @php
        return new Response('/?message=', 'danger.Nemáte přístup k zobrazení stránky', '#');
    @endphp
@endif
@if ($message->hasAny())
    @component('component.message')@endcomponent
@endif
<div class="article-list">
    <div class="container-fluid features-boxed">
        <div class="row" style="padding-top: 16px;">
            <div class="col-md-6 col-xl-3 offset-xl-1">
                <img class="img-fluid" src="@asset('img/avatars/'.$member->avatar)" alt="member-avatar" />
            </div>
            <div class="col-xl-6 offset-xl-0 member_page_info" style="margin-top: 1vh;">
                <p>Uživatel: <a href="/member/{{$member->username}}">{{ $member->username }}</a></p>
                @if ($member->visible)
                    <p>Jméno: {{$member->memberName}}</p>
                    <p>Příjmení: {{$member->memberSurname}}</p>
                    <p>Datum narození: {{$member->age}}</p>
                    <p>Město: {{$member->location}}</p>
                @endif
            </div>
        </div>
    </div>
</div>
