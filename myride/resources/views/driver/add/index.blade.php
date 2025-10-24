@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/driver'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('driver.add.usecases.post_driver')
        </div>
    </div>
@endsection