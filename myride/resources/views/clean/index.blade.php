@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <button class="btn btn-nav-page" onclick="window.location.href='/'"><i class="fa-solid fa-house"></i> Back to Home</button>
        @include('clean.usecases.get_clean_list')
    </div>
@endsection