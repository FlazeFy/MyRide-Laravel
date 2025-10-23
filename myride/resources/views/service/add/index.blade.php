@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = '<?= session()->get("token_key"); ?>'
    </script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/service'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('service.add.usecases.post_service')
        </div>
    </div>
@endsection