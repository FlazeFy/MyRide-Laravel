@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <button class="btn btn-nav-page" onclick="window.location.href='/trip'"><i class="fa-solid fa-arrow-left"></i> Back to Trip</button>
        @include('trip.add.usecases.post_trip')
    </div>
@endsection
