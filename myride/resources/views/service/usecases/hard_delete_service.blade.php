<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')
        const token = "<?= session()->get('token_key'); ?>"

        build_delete_modal(url, context, token, () => get_all_service(1))
    });
</script>