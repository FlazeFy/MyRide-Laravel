@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-danger" href='/clean'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('clean.add.usecases.post_clean')
        </div>
    </div>
@endsection