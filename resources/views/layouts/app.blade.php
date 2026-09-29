<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>@yield('title')</title>
</head>

<body style="background-color: gray">




    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('posts.index') }}">Publishing News</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavigation">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="{{ route('posts.index') }}">All posts</a></li>
            @auth
              @if (auth()->user()->hasVerifiedEmail())
                <li class="nav-item"><a class="nav-link" href="{{ route('posts.create') }}">Create post</a></li>
              @endif
            @endauth
          </ul>
          <ul class="navbar-nav align-items-lg-center gap-lg-2">
            @guest
              <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
            @else
              <li class="nav-item"><span class="navbar-text">{{ auth()->user()->name }}</span></li>
              <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="btn btn-outline-light btn-sm" type="submit">Log out</button>
                </form>
              </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
      @endif
        @yield('content')

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>