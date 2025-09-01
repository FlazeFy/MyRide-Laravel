@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <button class="btn btn-danger" onclick="window.location.href='/trip'"><i class="fa-solid fa-arrow-left"></i> Browse All Trip</button>
        <div class="container w-100 mt-4">
            @include('trip.add.usecases.post_trip')
        </div>
    </div>
@endsection
