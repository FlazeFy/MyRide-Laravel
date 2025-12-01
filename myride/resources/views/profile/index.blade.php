@extends('layouts.main_layout')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="container-fluid">
                @include('profile.usecases.get_active_req')
                @include('profile.usecases.get_profile')
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            
        </div>
    </div>
@endsection