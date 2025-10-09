@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-danger" href='/fuel'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('fuel.add.usecases.post_fuel')
        </div>
    </div>
@endsection