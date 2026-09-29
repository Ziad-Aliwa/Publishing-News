<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>@yield('title', 'Publishing News')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body @yield('page_class')">
    <div class="site-frame">
        <header class="site-header">
            <div class="site-header-inner">
                <a class="brand-lockup" href="{{ route('posts.index') }}" aria-label="Publishing News newsroom">
                    <span class="brand-mark">PN</span>
                    <span class="brand-copy">
                        <strong>Publishing News</strong>
                        <small>THE OPEN NEWSROOM</small>
                    </span>
                </a>

                <button class="nav-toggle navbar-toggler" type="button" data-menu-toggle="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation">
                    <span>Menu</span>
                </button>

                <nav class="collapse navbar-collapse site-navigation" id="mainNavigation" aria-label="Main navigation">
                    <div class="navigation-links">
                        <a class="navigation-link" href="{{ route('posts.index') }}">Newsroom</a>
                        @auth
                            @if (auth()->user()->hasVerifiedEmail())
                                <a class="navigation-link" href="{{ route('posts.create') }}">Write a story <span aria-hidden="true">+</span></a>
                            @endif
                        @endauth
                    </div>
                    <div class="navigation-account">
                        @guest
                            <a class="navigation-link" href="{{ route('login') }}">Log in</a>
                            <a class="button button-small" href="{{ route('register') }}">Join the newsroom</a>
                        @else
                            <span class="account-name">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="button button-quiet button-small" type="submit">Log out</button>
                            </form>
                        @endguest
                    </div>
                </nav>
            </div>
        </header>

        <main class="page-content">
            @if (session('status'))
                <div class="notice notice-success" role="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>

        <footer class="site-footer">
            <span>Publishing News <span aria-hidden="true">·</span> Independent voices, carefully published.</span>
            <a href="{{ route('posts.index') }}">Back to the newsroom</a>
        </footer>
    </div>

</body>

</html>