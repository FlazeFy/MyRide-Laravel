<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')

        buildDeleteModal(url, context, token, () => window.location.href = `/garage/detail/<?= $id ?>`)
    });
</script>