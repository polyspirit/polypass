<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if (config('app.robots'))
        <meta name="robots" content="all">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>{{ config('app.name', 'PolyPassword') }}</title>

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/4212cb2b11.js" crossorigin="anonymous"></script>
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/2.0.3/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/2.0.3/quill.min.js"></script>
    <!-- Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body data-bs-theme="{{ request()->cookie('theme') }}">
    <div id="app">
        <div class="notices js-notices"></div>
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}" title="{{ __('global.dashboard') }}">
                    <img src="/images/logo.png" alt="{{ config('app.name', 'Polypass') }}">
                    {{ config('app.name', 'Polypass') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                        @include('layouts.menu')
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('global.theme') }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('theme', ['theme' => 'light']) }}">
                                    <i class="fa-solid fa-sun w-15" aria-hidden="true"></i>
                                    {{ __('global.theme-light') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('theme', ['theme' => 'dark']) }}">
                                    <i class="fa-solid fa-moon w-15" aria-hidden="true"></i>
                                    {{ __('global.theme-dark') }}
                                </a>
                            </div>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('signin.login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ '/users/' . auth()->user()->id }}">
                                        <i class="fa-solid fa-user w-15"></i>
                                        {{ __('users.profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket w-15"></i>
                                        {{ __('signin.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @if (config('app.demo'))
            <div class="container mt-3">
                <div class="alert alert-warning" role="alert">
                    <h2>{{ __('warnings.warning') }}!</h2>
                    <b>{{ __('warnings.demo') }}</b>
                </div>
            </div>
        @endif

        @guest
            <main class="py-4">
                @yield('content')
            </main>
        @endguest

        @auth
            <main class="py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h1>
                                        {{ $title ?? __('global.dashboard') }}
                                    </h1>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            @if (isset($title))
                                                <li class="breadcrumb-item">
                                                    <a href="/">{{ __('global.dashboard') }}</a>
                                                </li>
                                            @endif
                                            @if (isset($breadcrumbs))
                                                @foreach ($breadcrumbs as $bcUrl => $bcTitle)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ $bcUrl }}">{{ $bcTitle }}</a>
                                                    </li>
                                                @endforeach
                                            @endif
                                            <li class="breadcrumb-item active" aria-current="page">
                                                {{ $title ?? __('global.dashboard') }}
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            <h5 class="mb-0">{{ session('status') }}</h5>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        @endauth
    </div>
</body>

</html>
