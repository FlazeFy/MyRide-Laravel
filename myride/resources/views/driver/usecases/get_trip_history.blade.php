<div class="modal fade" id="trip_history-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel"><span id="username_trip_history-holder"></span>'s Trip History</h5>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div id="trip-content-holder"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.btn-history-trip',function(){
        const id = $(this).data('id')
        const username = $(this).data('username')
        
        $('#username_trip_history-holder').text(username)

        $.ajax({
            url: `/api/v1/trip/driver/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                $('#trip-content-holder').empty()
                data.forEach(dt => {
                    $('#trip-content-holder').append(template_trip_box(dt))
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong",
                    icon: "error"
                });
            }
        });
    })
</script>