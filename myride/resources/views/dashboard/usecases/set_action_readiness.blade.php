<div class="modal fade" id="action_readiness-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel"><span id="action_vehicle_name-holder"></span>'s Action</h5>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <p>What are you want to do with <span id="action_vehicle_name_plate-holder"></span>?</p>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <a class="btn btn-success w-100 mb-2" id="set_trip_action-button"><i class="fa-solid fa-suitcase"></i> Set a Trip</a>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <a class="btn btn-success w-100" id="buy_fuel_action-button"><i class="fa-solid fa-gas-pump"></i> Buy a Fuel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click','.btn-action-readiness',function(){
            const id = $(this).data('id')
            const vehicle_name = $(this).data('vehicle_name')
            const vehicle_plate_number = $(this).data('vehicle_plate_number')

            $('#action_vehicle_name-holder').text(vehicle_name)
            $('#action_vehicle_name_plate-holder').html(`${vehicle_name} <b>(${vehicle_plate_number})</b>`)
            $('#set_trip_action-button').attr('href',`/trip/add?vehicle_id=${id}`)
            $('#buy_fuel_action-button').attr('href',`/fuel/add?vehicle_id=${id}`)
            $('#action_readiness-modal').modal('show')
        })  
    })
</script>