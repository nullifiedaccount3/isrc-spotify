@extends('base')

@section('nav')
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="/exporter">
                        <span data-feather="home"></span>
                        Export History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{url('/user/profile')}}">
                        <span data-feather="user"></span>
                        User Profile<span class="sr-only">(current)</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">

                <div class="panel panel-default" style="margin-top: 200px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-8">
                                <p>Name: - {{$user->name}} -</p>
                                <p>Nickname: - {{$user->nickname}} -</p>
                                <p>Email: - {{$user->email}} -</p>
                                <p><a target="_blank" href="{{$user->spotify_profile_url}}">
                                        Spotify Profile <span data-feather="link"></span></a></p>
                            </div><!--/col-->
                            <div class="col-xs-12 col-sm-4 text-center">
                                @if(!empty($user->avatar))
                                    <img src="{{$user->avatar}}" alt="{{$user->name}}"
                                         class="center-block img-circle img-responsive"
                                         style="min-width: 150px;">
                                @else
                                    <img src="{{asset('/img/user.svg')}}" alt="{{$user->name}}"
                                         class="center-block img-circle img-responsive"
                                         style="min-width: 150px;">
                                @endif
                            </div><!--/col-->
                        </div><!--/row-->
                    </div><!--/panel-body-->
                </div><!--/panel-->


            </div>
        </div>
    </div>
@endsection
