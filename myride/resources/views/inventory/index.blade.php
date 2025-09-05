@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-lg-9 col-md-7 col-sm-12">
                <div class="container">
                    @include('inventory.usecases.get_all_list_inventory')
                </div>
            </div>
        </div>
    </div>
@endsection