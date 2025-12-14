<h2 class="mb-3">Summary Trip</h2><hr>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Person With</h5>
        <h4 class="fw-bold" id="most_person_with"></h4>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Total Distance</h5>
        <h4 class="fw-bold" id="vehicle_total_trip_distance"></h4>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Origin</h5>
        <h4 class="fw-bold" id="most_origin"></h4>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Destination</h5>
        <h4 class="fw-bold" id="most_destination"></h4>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Category</h5>
        <h4 class="fw-bold" id="most_category"></h4>
    </div>
</div>

<script>
    const get_vehicle_summary_trip_by_id = (id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/trip/summary/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const data = response.data

                $('#most_person_with').text(data.most_person_with ?? '-')
                $('#most_origin').text(data.most_origin ?? '-')
                $('#most_destination').text(data.most_destination ?? '-')
                $('#most_category').text(data.most_category ?? '-')
                $('#vehicle_total_trip_distance').text(`${data.vehicle_total_trip_distance ?? '-'} Km`)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generateApiError(response, true)
            }
        });
    }
</script>