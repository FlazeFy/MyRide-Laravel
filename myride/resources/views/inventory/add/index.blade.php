@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/inventory'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('inventory.add.usecases.post_inventory')
        </div>
    </div>
@endsection