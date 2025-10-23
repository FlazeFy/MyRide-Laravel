<h5 class="mb-4">Summary Trip</h5><hr>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Person With</h5>
        <h2 class="fw-bold" id="most_person_with"></h2>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Total Distance</h5>
        <h2 class="fw-bold" id="vehicle_total_trip_distance"></h2>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Origin</h5>
        <h2 class="fw-bold" id="most_origin"></h2>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Destination</h5>
        <h2 class="fw-bold" id="most_destination"></h2>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6 mx-auto">
        <h5>Most Category</h5>
        <h2 class="fw-bold" id="most_category"></h2>
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
                Swal.close()

                if(response.status !== 404){
                    failedMsg('get the summary of trip')
                } else {
                    
                }
            }
        });
    }
    get_vehicle_summary_trip_by_id("<?= $id ?>")
</script>