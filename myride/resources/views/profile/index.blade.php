@extends('layouts.main_layout')

@section('content')
    <div class="p-3">
        <button class="btn btn-nav-page" onclick="window.location.href='/'" style="top: var(--spaceMD); left: var(--spaceMD);"><i class="fa-solid fa-house"></i> Back to Home</button>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('profile.usecases.get_active_req')
                @include('profile.usecases.get_profile')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                
            </div>
        </div>
    
    </div>
@endsection