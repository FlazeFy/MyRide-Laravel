@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/clean'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('clean.add.usecases.post_clean')
        </div>
    </div>
@endsection