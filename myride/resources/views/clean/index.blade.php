@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        @include('clean.usecases.get_export_clean')
        <div class="container mt-2">
            @include('clean.usecases.get_all_list_clean')
        </div>
    </div>
@endsection