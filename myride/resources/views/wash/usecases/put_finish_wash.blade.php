<script>
    $(document).on('click','.btn-finish',function(){
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to mark this wash data as finished?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, finish it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                put_wash($(this).data('id'))
            }
        });
    })

    const put_wash = (id) => {
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/wash/finish/${id}`,
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
                    window.location.href = '/wash'
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generateApiError(response, true)
            }
        });
    }
</script>