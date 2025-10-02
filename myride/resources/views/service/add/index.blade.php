@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container">
            @include('service.add.usecases.post_service')
        </div>
    </div>
@endsection