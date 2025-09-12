@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        @include('inventory.usecases.get_export_inventory')
        <div class="row mt-2">
            <div class="col-lg-9 col-md-7 col-sm-12">
                <div class="container">
                    @include('inventory.usecases.get_all_list_inventory')
                    @include('inventory.usecases.hard_delete_inventory')
                </div>
            </div>
        </div>
    </div>
@endsection