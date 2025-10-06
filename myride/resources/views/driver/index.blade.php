@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container">
            @include('driver.usecases.get_all_list_driver')
            @include('driver.usecases.hard_delete_driver')
        </div>
    </div>
@endsection