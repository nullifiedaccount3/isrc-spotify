@extends('base')

@section('top-nav')
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/">ISRC Exporter</a>
        <form style="width: 100%" id="search" action="{{url('/search')}}" method="get">
            <input {{Auth::check()?'':'disabled'}} name="q" class="form-control form-control-dark w-100" type="text"
                   placeholder="Search"
                   aria-label="Search">
        </form>
        @if(Auth::check())
            <ul class="navbar-nav px-3">
                <li class="nav-item text-nowrap">
                    <a class="nav-link" href="{{url('/logout')}}">Sign out</a>
                </li>
            </ul>
        @endif
    </nav>
@endsection

@section('nav')
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                @if(Auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="/exporter">
                            <span data-feather="home"></span>
                            Export History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/user/profile')}}">
                            <span data-feather="user"></span>
                            User Profile<span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{url('/docs')}}">
                            <span data-feather="help-circle"></span>
                            Help<span class="sr-only">(current)</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h1>Help</h1>
                <h4>Login</h4>
                <ul>
                    <li>Login using Spotify, no signup is required</li>
                </ul>
                <h4>ISRC Search</h4>
                <ul>
                    <li>Fetch track ISRC code using a simple search box powered by Spotify</li>
                    <li>Type your query partial or full in the top search bar and hit return key</li>
                    <li>ISRC exporter will start processing the file</li>
                    <li>Once the file is ready the app will display a link to download it automatically. No
                        need for a page refresh
                    </li>
                </ul>
                <h4>File format</h4>
                <ul>
                    <li>The file is provided in TSV, Tab Separated Values, format</li>
                    <li>Structure: <b>Index</b>, <b>Track Album Art</b>, <b>Artist name</b>, <b>Album Name</b>, <b>Track
                            Name</b>, <b>ISRC</b> and <b>Track Duration</b></li>
                </ul>
                <h4>Questions</h4>
                <ul>
                    <li>Please contact Bhargav Nanekalva (bhargav3@gmail.com)</li>
                </ul>

            </div>
        </div>
    </div>
@endsection
