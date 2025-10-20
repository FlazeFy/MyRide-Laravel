@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = '<?= session()->get("token_key"); ?>'
    </script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/fuel'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('fuel.add.usecases.post_fuel')
        </div>
    </div>
@endsection