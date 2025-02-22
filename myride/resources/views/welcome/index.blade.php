@extends('layouts.main_layout')

@section('content')
    <div class="position-relative text-center pt-4">
        @include('welcome.welcoming')
        <br><br>
        @include('welcome.services')
    </div>
@endsection