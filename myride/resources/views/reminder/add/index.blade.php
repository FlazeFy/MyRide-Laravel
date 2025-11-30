@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
        const reminder_holder = 'reminder-holder'
    </script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/reminder'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container-fluid mt-2">
            @include('reminder.add.usecases.post_reminder')
        </div>
    </div>
@endsection