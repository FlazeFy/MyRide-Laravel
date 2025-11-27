<div id="recover_vehicle_button-holder"></div>
<script>
    $(document).on('click', '.btn-recover', function () {
        Swal.fire({
            title: "Are you sure?",
            text: `Do you want to recover this "vehicle"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, recover it",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/api/v1/vehicle/recover/<?= $id ?>",
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json")
                        xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your vehicle has been recovered",
                            icon: "success",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload()
                            }
                        })

                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        generate_api_error(response, true)
                    }
                });
            }
        });
    });
</script>