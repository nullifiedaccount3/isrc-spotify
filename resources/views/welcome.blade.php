<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ISRC Exporter</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
    <link rel="stylesheet" href="{{asset('bootstrap-4.1.2-dist/css/bootstrap.min.css')}}"/>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        @if (Auth::check())
            <div class="top-right links">
                @auth
                    <a href="{{ url('/logout') }}">Logout</a>
                @endauth
            </div>
        @endif
        <div class="title m-b-md">
            ISRC Exporter
        </div>

        <div class="links">
            @if(Auth::check())
                <button type="button" class="btn btn-primary" onclick="location.href='{{url('/exporter')}}'">
                    Exporter
                </button>
            @else
                <button type="button" class="btn btn-primary" onclick="location.href='{{url('/login/spotify')}}'">
                    Login With Spotify
                </button>
            @endif
            <button type="button" class="btn btn-primary" onclick="location.href='{{url('/docs')}}'">
                Documentation
            </button>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{asset('bootstrap-4.1.2-dist/js/bootstrap.min.js')}}"></script>
</body>
</html>
