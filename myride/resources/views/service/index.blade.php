@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = '<?= session()->get("token_key"); ?>'
    </script>

    <div class="position-relative">
        <a class="btn btn-success" href='/service/add'><i class="fa-solid fa-plus"></i> Service</a>
        @include('service.usecases.get_export_service')
        <div class="row mt-2">
            <div class="col-lg-8 col-md-12">
                <div class="container">
                    @include('service.usecases.get_all_list_service')
                    @include('service.usecases.hard_delete_service')
                    @include('service.usecases.put_service')
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class="container text-center">
                            @include('service.usecases.get_total_service_price_context')
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class="container">
                            @include('service.usecases.get_list_service_spending_per_vehicle')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection