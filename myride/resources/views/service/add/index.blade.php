@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-danger" href='/service'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('service.add.usecases.post_service')
        </div>
    </div>
@endsection