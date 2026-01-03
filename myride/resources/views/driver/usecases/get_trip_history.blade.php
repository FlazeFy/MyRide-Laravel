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
    const items_holder_history = 'trip-content-holder'

    $(document).on('click','.btn-history-trip',function(){
        const id = $(this).data('id')
        const username = $(this).data('username')
        let page = 1
        
        $('#username_trip_history-holder').text(username)
        get_all_trip_by_driver_id(page, id)
    })

    const get_all_trip_by_driver_id = (page, id) => {
        $(`#${items_holder_history}`).empty()
        $.ajax({
            url: `/api/v1/trip/driver/${id}?page=${page}`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                const total_page = response.data.last_page
                const current_page = response.data.current_page

                data.forEach(dt => {
                    $(`#${items_holder_history}`).append(templateTripBox(dt,null,false))
                });

                if(total_page > 1){
                    generatePagination(items_holder_history,(selectedPage) => { get_all_trip_by_driver_id(selectedPage,id) }, total_page, current_page)
                }
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    generateApiError(response, true)
                } else {
                    messageAlertBox(items_holder_history, "danger", "No trip history found for this driver")
                }
            }
        });
    }
</script>