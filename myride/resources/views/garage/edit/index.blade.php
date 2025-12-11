@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    @include('garage.back_garage_button')
    <div class="p-2">
        @include('garage.edit.usecases.get_props')
    </div>
    <div class="container-fluid">
        @include('garage.edit.usecases.put_vehicle_data')
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="container-fluid">
                <h2>Vehicle Image</h2><hr>
                @include('garage.edit.usecases.put_vehicle_image')
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="container-fluid">
                @include('garage.edit.usecases.put_vehicle_doc')
            </div>
        </div>
    </div>

    <script>
        let vehicle_img_url = null

        const get_vehicle = (id) => {
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/vehicle/detail/${id}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    const carnametransmission = data.vehicle_name.split(" ")
                    const transmission = carnametransmission[carnametransmission.length - 1]
                    const carname = carnametransmission.slice(0, -1).join(" ")

                    $(`#vehicle_name`).val(carname)
                    $('#vehicle_transmission_code').val(transmission)
                    $(`#vehicle_merk`).html(`<option value='${data.vehicle_merk}'>${data.vehicle_merk}</option>`)
                    $(`#vehicle_type`).html(`<option value='${data.vehicle_type}'>${data.vehicle_type}</option>`)
                    $(`#vehicle_category`).html(`<option value='${data.vehicle_category}'>${data.vehicle_category}</option>`)
                    $(`#vehicle_default_fuel`).html(`<option value='${data.vehicle_default_fuel}'>${data.vehicle_default_fuel}</option>`)
                    $(`#vehicle_fuel_status`).html(`<option value='${data.vehicle_fuel_status}'>${data.vehicle_fuel_status}</option>`)
                    $(`#vehicle_status`).html(`<option value='${data.vehicle_status}'>${data.vehicle_status}</option>`)
                    $(`#vehicle_color`).html(`<option value='${data.vehicle_color}'>${data.vehicle_color}</option>`)
                    $(`#vehicle_price`).val(data.vehicle_price)
                    $(`#vehicle_desc`).val(data.vehicle_desc)
                    $(`#vehicle_distance`).val(data.vehicle_distance)
                    $(`#vehicle_year_made`).val(data.vehicle_year_made)
                    $(`#vehicle_plate_number`).val(data.vehicle_plate_number)
                    $(`#vehicle_fuel_capacity`).val(data.vehicle_fuel_capacity)
                    $(`#vehicle_capacity`).val(data.vehicle_capacity)

                    if(data.vehicle_img_url){
                        $('#vehicle_img-holder').html(`<img class="img img-fluid" src="${data.vehicle_img_url}" alt="${data.vehicle_img_url}"/>`)
                        $('#add_image-button span').text(' Change Image')
                        $('#vehicle_image_button-holder').prepend(`<a class="btn btn-danger py-1" id="remove_image-button"><i class="fa-solid fa-trash"></i><span class="d-none d-md-inline"> Remove Image</span></a>`)
                        vehicle_img_url = data.vehicle_img_url
                    } else {
                        template_alert_container('vehicle_img-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
                    }

                    $('#created_at').text(data.created_at)
                    $('#updated_at').text(data.updated_at ?? '-')
                    $('#deleted_at').text(data.deleted_at ?? '-')
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        generate_api_error(response, true)
                    } else {
                        failedRoute('vehicle','/garage')
                    }
                }
            });
        }
        get_vehicle('<?= $id ?>')
    </script>
@endsection