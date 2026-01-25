<h2>Add Fuel</h2><hr>
<form id="form-add-fuel">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <hr>
            @include('fuel.add.usecases.get_last_purchase')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Fuel Brand</label>
                    <select class="form-select" name="fuel_brand" id="fuel_brand_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Fuel Type</label>
                    <select class="form-select" name="fuel_type" id="fuel_type_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                    <label>Fuel RON</label>
                    <input class="form-control" name="fuel_ron" id="fuel_ron">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                    <label>Volume (L)</label>
                    <input class="form-control" name="fuel_volume" id="fuel_volume" type="number" min="1" value="1">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <label>Total Price</label>
                            <input class="form-control form-validator" name="fuel_price_total" id="fuel_price_total" type="number" data-validator="must_positive" min="0">
                        </div>
                        <div class="col-sm-6 col-12">
                            <label>Fuel At</label>
                            <input class="form-control form-validator" data-validator="must_past" name="fuel_at" id="fuel_at" type="datetime-local">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            @include('fuel.add.usecases.select_fuel_bill')
            <div class="d-grid d-md-inline-block">
                <a class="btn btn-success rounded-pill py-3 w-100 w-md-auto mt-3" id="submit-add-fuel-btn"><i class="fa-solid fa-floppy-disk"></i> Save Fuel</a>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    setCurrentLocalDateTime("fuel_at")

    $(document).on('click','#submit-add-fuel-btn', function(){
        post_fuel()
    })

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        getVehicleDetail(id)
        id !== "-" && get_vehicle_last_fuel(id)
    })

    $(document).on('change','#fuel_brand_holder', async function(){
        const val = $(this).val()
        await getDictionaryByContextOption(`fuel_type_${val}`)
    })

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
    ;(async () => {
        await getDictionaryByContextOption('fuel_brand',token)
    })()

    const post_fuel = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const fuel_brand = $('#fuel_brand_holder').val()
        const fuel_type = $('#fuel_type_holder').val()

        if (vehicle_id === "-" || fuel_brand === "-") {
            failedMsg('create fuel : you must select an item')
            return
        }

        const fd = new FormData()

        fd.append("vehicle_id", vehicle_id)
        fd.append("fuel_brand", fuel_brand)
        fd.append("fuel_type", fuel_type === "-" ? null : fuel_type)
        fd.append("fuel_ron", $("#fuel_ron").val())
        fd.append("fuel_volume", $('#fuel_volume').val())
        fd.append("fuel_at", $('#fuel_at').val())
        fd.append("fuel_price_total", $('#fuel_price_total').val())

        const img = $("#fuel_bill")[0] ? $("#fuel_bill")[0].files[0] : null
        fd.append("fuel_bill", img ? img : null)

        $.ajax({
            url: `/api/v1/fuel`,
            type: 'POST',
            processData: false,
            contentType: false,
            data: fd,
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                Swal.fire("Success!", response.message,"success").then(() => {
                    window.location.href = '/fuel'
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