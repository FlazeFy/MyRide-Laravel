<div class="modal fade" id="trip_coordinate-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Trip History Coordinate</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <p>We found some trip history locations with names similar to your current trip plan. Would you like to use these coordinates?</p>
                <div id="trip_location-holder"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let page = 1
    const holder = '#trip_location-holder'
    
    $(document).on('blur','#trip_origin_name,#trip_destination_name',function(){
        const locationName = $(this).val()

        if(locationName.trim() !== ""){
            $(holder).empty()
            getTripCoordinateByLocationName(locationName, $(this).attr('id'), page)
        }
    })

    const getTripCoordinateByLocationName = (locationName, id, page) => {
        $.ajax({
            url: `/api/v1/trip/coordinate/${locationName}?page=${page}`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                const lastPage = response.data.last_page
                
                data.data.forEach(dt => {
                    $(holder).append(`
                        <div class="container-fluid p-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2 align-items-center">
                                <h6 class="mb-0">${dt.trip_location_name}</h6>
                                ${locationName.toLowerCase() === dt.trip_location_name.toLowerCase() ? `<span class="chip bg-success m-0">Matched</span>`:""}
                            </div>
                            <p class="text-secondary mb-1">${dt.trip_location_coordinate}</p>
                            <a class="btn btn-success py-1 px-3 use_coordinate-btn" data-location-coordinate="${dt.trip_location_coordinate}" data-location-name="${dt.trip_location_name}" data-active-input="${id}" style="font-size: var(--textMD)">Use This</a>
                        </div>
                    `)
                })
                callModal('trip_coordinate-modal')

                if (lastPage > page){
                    $(holder).append(`<a class="btn btn-warning py-1 mx-auto see_more-button" data-location-name="${locationName}" data-id="${id}" data-current-page="${page}"><i class="fa-solid fa-magnifying-glass"></i> See More</a>`)
                }
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generateApiError(response, true)
                } 
            }
        })
    }

    $(document).ready(() => {
        $(document).on('click','.use_coordinate-btn',function(){
            const coordinate = $(this).data('location-coordinate')
            const locName = $(this).data('location-name')
            const id = $(this).data('active-input')

            $(`#${id.replace('name','coordinate')}`).val(coordinate)
            $(`#${id}`).val(locName)
            const [lat, lng] = coordinate.split(',').map(c => parseFloat(c.trim()))
            placeMarkerAndPanTo({lat: lat, lng: lng}, map, id.includes('origin') ? 'Origin' : 'Destination')

            $('#trip_coordinate-modal').modal('hide')
        })

        $(document).on('click', '.see_more-button', function(){
            const locationName = $(this).data('location-name')
            const id = $(this).data('id')
            const page = parseInt($(this).data('current-page')) + 1

            $(`${holder} .see_more-button`).remove()
            getTripCoordinateByLocationName(locationName, id, page)
        })
    })
</script>