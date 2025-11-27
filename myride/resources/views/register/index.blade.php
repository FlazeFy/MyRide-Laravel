@extends('layouts.sign_up_layout')

@section('content')
    <div class="container d-block mx-auto" style="max-width: 720px;"> 
        @include('register.usecases.stepper')
        <div id="tnc-section">
            @include('register.usecases.tnc')
        </div>
        <div class="d-none" id="profile-section">
            @include('register.usecases.profile')
        </div>
        <div class="d-none" id="token-section">
            @include('register.usecases.token')
        </div>
        <div class="d-none" id="service-section">
            @include('register.usecases.service')
        </div>
    </div>
@endsection