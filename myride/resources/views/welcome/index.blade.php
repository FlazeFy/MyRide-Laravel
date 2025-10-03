@extends('layouts.main_layout')

@section('content')
    <div class="position-relative text-center">
        @include('welcome.usecases.welcoming')
        <br><br>
        @include('welcome.usecases.services')
        <br><br>
        @include('welcome.usecases.summary')
        <br><br>
        @include('welcome.usecases.faq')
    </div>
@endsection