<script>
    $(document).on('click','.btn-remove-assigned-driver',function(){
        const id = $(this).data('id')
        const vehicle = $(this).data('vehicle')
        const driver = $(this).data('driver')

        Swal.fire({
            title: "Are you sure!",
            html: `want remove ${driver} from ${vehicle}?`,
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading()
                $.ajax({
                    url: `/api/v1/driver/destroy/relation/${id}`,
                    type: 'DELETE',
                    contentType: "application/json",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json")
                        xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                    },
                    success: function(response) {
                        Swal.close()
                        Swal.fire("Success!", response.message, "success").then(() => {
                            window.location.href = '/driver'
                        })
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        Swal.close()
                        if(response.status === 500){
                            generateApiError(response, true)
                        } else {
                            failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                        }
                    }
                })
            }
        })
    })
</script>