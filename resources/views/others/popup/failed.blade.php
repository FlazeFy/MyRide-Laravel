@if (session('failed_message'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: '{{ session('failed_message') }}',
            confirmButtonText: 'Okay'
        })
    </script>
@endif