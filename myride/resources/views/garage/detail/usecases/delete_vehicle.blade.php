<div id="delete_vehicle_button-holder"></div>
<script>
    $(document).on('click', '.btn-delete', function () {
        const url = $(this).data('url')
        const context = $(this).data('context')
        const type_delete = $(this).data('type-delete')
        const redirect_url = type_delete === 'hard' ? '/garage' : `/garage/detail/<?= $id ?>`

        build_delete_modal(url, context, token, () => window.location.href=redirect_url)
    });
</script>