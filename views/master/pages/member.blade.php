@use(Mlkali\Sa\Http\Response)
@if(isset($selector->article) && !$member->logged) 
    @php
        $memberData = $member->getMemberInfo('username', $selector->article);
        
        if($memberData)
        {
            $member->logged = true;

            foreach($memberData as $key => $value)
            {
                $member->{$key} = $value;
            }   
        }
        else
        {
            return new Response('/register?message=', 'danger.Uživatel neexistuje', '#register');
        }
    @endphp
@elseif(!isset($selector->article))
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
            <div class="col-xl-9 offset-xl-1">
                <h2 style="margin-top:10vh;">Uložené záložky </h2>
                <p style="color:#73b6ff">* Maximální počet záložek je 12. Uložit záložku lze pouze při čtení příběhu v menu uživatele. 
                <div class="row justify-content-center features" id="bookmarks">
                    @if($member->bookmarkCount == 0)
                    <div class="col-sm-6 col-md-5 col-lg-4 item">
                        <div class="box">
                            <a class="learn-more" style="pointer-events: none; cursor: default;"> Nemáte žádnou uloženou záložku </a>
                        </div>
                    </div>
                    @else
                    @foreach(json_encode($member->bookmarks, true) as $key => $value)
                    <div class="col-sm-6 col-md-5 col-lg-4 item">
                        <div class="box">
                            <a class="learn-more" href="{{$value}}">Záložka - {{$key}} »</a>
                        <br>
                        <a class="learn-more" href="/removebookmark?x={{$key}}&y={{$enc->encrypt($member->memberID)}}">Smazat</a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>