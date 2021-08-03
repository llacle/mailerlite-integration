<html>
    <head>
        <title>MailerLite App - @yield('title')</title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
          <div class="container-xl">
            <a class="navbar-brand" href="/">MailerLite App</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item {{ request()->is('subscribers') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('/subscribers') }}">Subscribers</a>
                </li>
                <li class="nav-item {{ request()->is('apikey') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('/apikey') }}">API Key</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <div class="container">
            @yield('content')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('footer_scripts')
    </body>
</html>
