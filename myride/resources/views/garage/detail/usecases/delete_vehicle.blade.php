<a class="btn btn-danger btn-delete" data-context="Vehicle" data-url="/api/v1/vehicle/delete/<?= $id ?>"><i class="fa-solid fa-trash"></i> Delete</a>
<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')
        const token = "<?= session()->get('token_key'); ?>"

        build_delete_modal(url, context, token, () => window.location.href='/garage')
    });
</script>