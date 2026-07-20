@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto" style="max-width: 720px;">
        <div class="container-fluid">
            @include('about.usecases.about_apps')
        </div>
        <div class="container-fluid">
            @include('about.usecases.about_feature')
        </div>
        <div class="container-fluid">
            @include('about.usecases.about_creator')
        </div>
        <div class="container-fluid">
            @include('about.usecases.about_stack')
        </div>
    </div>
@endsection