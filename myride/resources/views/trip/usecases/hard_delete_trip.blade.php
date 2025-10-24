<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')

        build_delete_modal(url, context, token, () => get_all_trip(1))
    });
</script>