<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')
        const token = "<?= session()->get('token_key'); ?>"

        buildDeleteModal(url, context, token, () => get_all_fuel(1))
    });
</script>