@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-success" href='/inventory/add'><i class="fa-solid fa-plus"></i> Add Inventory</a>
        @include('inventory.usecases.get_export_inventory')
        <div class="row mt-2">
            <div class="col-lg-9 col-md-7 col-sm-12">
                <div class="container">
                    @include('inventory.usecases.get_all_list_inventory')
                    @include('inventory.usecases.hard_delete_inventory')
                    @include('inventory.usecases.put_inventory')
                </div>
            </div>
        </div>
    </div>
@endsection