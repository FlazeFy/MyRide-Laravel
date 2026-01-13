<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Driver</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="driver_id">
                <label>Fullname</label>
                <input class="form-control" name="fullname" id="fullname">
                <label>Username</label>
                <input class="form-control" name="username" id="username">
                <label>Email</label>
                <input class="form-control" name="email" id="email" type="email">
                <label>Phone</label>
                <input class="form-control" name="phone" id="phone">
                <label>Notes</label>
                <textarea class="form-control" name="notes" id="notes"></textarea>
                <hr>
                <button class="btn btn-success rounded-pill px-4 w-100" id="submit_update-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.btn-update',function(){
        callModal('update-modal')

        $('#driver_id').val($(this).data('id'))
        $('#username').val($(this).data('username'))
        $('#fullname').val($(this).data('fullname'))
        $('#email').val($(this).data('email'))
        $('#phone').val($(this).data('phone'))
        $('#notes').val($(this).data('notes'))
    })

    $(document).on('click','#submit_update-btn', function(){
        const id = $('#driver_id').val()
        put_driver(id)
    })
    const put_driver = (id) => {
        const vehicle_id = $('#vehicle_holder').val()
        const driver_category = $('#driver_category_holder').val()

        Swal.showLoading()
        $.ajax({
            url: `/api/v1/driver/${id}`,
            type: 'PUT',
            contentType: "application/json",
            data: JSON.stringify({
                vehicle_id: vehicle_id,
                username: $("#username").val(),
                fullname: $("#fullname").val(),
                email: $("#email").val(),
                phone: $("#phone").val(),
                notes: $("#notes").val()
            }),
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
        });
    }
</script>