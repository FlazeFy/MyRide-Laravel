@extends('layouts.main_layout')

@section('content')
    <div class="p-3">
        <div class="row">
            <div class="col-lg-8 col-md-6 col-sm-12">
                @include('landing.usecases.get_menu')
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                @include('landing.usecases.get_profile_section')
            </div>
        </div>
        @include('landing.usecases.post_sign_out')
    </div>
@endsection