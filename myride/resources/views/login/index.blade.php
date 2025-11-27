@extends('layouts.sign_up_layout')

@section('content')
    <div class="container d-block mx-auto" style="max-width: 500px;"> 
        <h2 class="fw-bold" style="font-size:36px;">You are at MyRide Apps</h2>
        <h6>Management Apps for your vehicle</h6><br>
        @include('login.usecases.post_login')
    </div>
@endsection