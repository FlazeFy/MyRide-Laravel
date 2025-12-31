<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')

        buildDeleteModal(url, context, token, () => getAllWash(1))
    });
</script>