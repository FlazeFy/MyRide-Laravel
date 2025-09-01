@extends('layouts.main_layout')

@section('content')
    <div class="p-3">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="container">
                    @include('profile.usecases.get_active_req')
                    @include('profile.usecases.get_profile')
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                
            </div>
        </div>
    
    </div>
@endsection