@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container mt-2">
            @include('about.usecases.about_apps')
        </div>
        <div class="container mt-2">
            @include('about.usecases.feature_info')
        </div>
    </div>
@endsection