<script>
    $(document).on('click','.btn-finish',function(){
        const token = `<?= session()->get("token_key"); ?>`

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to mark this clean data as finished?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, finish it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                put_clean($(this).data('id'))
            }
        });
    })

    const put_clean = (id) => {
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/clean/finish/${id}`,
            type: 'PUT',
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                }).then(() => {
                    window.location.href = '/clean'
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status === 500){
                    failedMsg('update clean')
                } else {
                    failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                }
            }
        });
    }
</script>