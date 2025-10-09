@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-danger" href='/inventory'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('inventory.add.usecases.post_inventory')
        </div>
    </div>
@endsection