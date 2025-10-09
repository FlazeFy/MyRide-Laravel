@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-success" href='/clean/add'><i class="fa-solid fa-plus"></i> Add Clean</a>
        @include('clean.usecases.get_export_clean')
        <div class="container mt-2">
            @include('clean.usecases.get_all_list_clean')
            @include('clean.usecases.hard_delete_clean')
            @include('clean.usecases.put_finish_clean')
        </div>
    </div>
@endsection