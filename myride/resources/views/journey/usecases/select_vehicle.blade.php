<a class="btn btn-primary px-3 mt-2" data-bs-toggle="modal" data-bs-target="#select_vehicle-modal" id="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search now!</a>
<div class="modal fade" id="select_vehicle-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Choose your ride</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p>Choose vehicle that you want to see the history</p>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
            </div>
        </div>
    </div>
<script>
    const init_vehicle_page = async (token) => {
        await getVehicleNameOption(token)

        if ($('#vehicle_holder').val() !== "-") {
            let params = new URLSearchParams(window.location.search)
            let vehicle_id = params.get('vehicle_id')

            if (vehicle_id) {
                getVehicleDetail(vehicle_id)
                get_vehicle_last_fuel(vehicle_id)
            }
        }
    }
    init_vehicle_page(token)
    
    $(document).ready(() => {
        $(document).on('change','#vehicle_holder',function(){
            const text = $(this).find('option:selected').text()
            $('#select_vehicle-modal').modal('hide')
            $('#search-btn').html(text !== "-" ? text : `<i class="fa-solid fa-magnifying-glass"></i> Search now!`)
        })
    })
</script>