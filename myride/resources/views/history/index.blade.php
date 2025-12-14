@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="mx-auto" style="max-width: 720px;">
        <div class="container-fluid">
            @include('history.usecases.get_all_list_history')
            @include('history.usecases.hard_delete_history')
        </div>
    </div>
@endsection