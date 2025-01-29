@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="d-block mx-auto p-3">
            <button class="btn btn-nav-page" onclick="window.location.href='/'"><i class="fa-solid fa-house"></i> Back to Home</button>
            @include('clean.usecases.get_clean_list')
        </div>
    </div>
@endsection