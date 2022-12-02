<header id="header">
    <div class="logo"><i class="far fa-star"></i></div>
    <div class="content">
        <div class="inner">
            <h1>Star Adventure</h1>
            <p>Dobrodružný / Sci-fi / Fantasy</p>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="/intro#intro">Intro</a></li>
            @if (!$member->logged)
            <li><a href="/register#register">Register</a></li>
            <li><a href="/login#login">Login</a></li>
            @else
            <li><a href="/member/{{$member->username}}">Profil</a></li>
            <li><a href="/logout">Odhlásit</a></li>
            @endif
            <li><a href="/storylist#storylist">Příběhy</a></li>
        </ul>
    </nav>
</header>