@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-success" href='/clean/add'><i class="fa-solid fa-plus"></i> Add Clean</a>
        @include('clean.usecases.get_export_clean')
        <div class="container mt-2">
            @include('clean.usecases.get_all_list_clean')
            @include('clean.usecases.hard_delete_clean')
        </div>
    </div>
@endsection