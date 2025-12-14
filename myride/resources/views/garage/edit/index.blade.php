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
                @include('garage.edit.usecases.put_vehicle_image_collection')
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="container-fluid">
                @include('garage.edit.usecases.put_vehicle_doc')
            </div>
        </div>
    </div>
    @include('garage.edit.usecases.delete_vehicle_document')

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
                    const carname = carnametransmission.slice(0, -1).join(" ")

                    $(`#vehicle_name`).val(carname)
                    $('#vehicle_transmission_code').val(data.vehicle_transmission)
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
                        templateAlertContainer('vehicle_img-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
                    }

                    if(data.vehicle_other_img_url){
                        data.vehicle_other_img_url.forEach(dt => {
                            $('#vehicle_img_collection-holder').append(`
                                <div class="col-md-6 col-sm-12">
                                    <div class="container-fluid">
                                        <img class="img img-fluid mb-2" src="${dt.vehicle_img_url}" alt="${dt.vehicle_img_url}"/>
                                        <a class="btn btn-danger btn-delete w-100" style="width:50px;" data-url="/api/v1/vehicle/image_collection/destroy/<?= $id ?>/${dt.vehicle_img_id}" data-context="Vehicle Image Collection"><i class="fa-solid fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            `)
                        });
                        $('#add_image_collection-button span').text(' Change Image')
                        $('#vehicle_image_collection_button-holder').prepend(`<a class="btn btn-danger py-1" id="remove_image_collection-button"><i class="fa-solid fa-trash"></i><span class="d-none d-md-inline"> Remove Image</span></a>`)
                        vehicle_other_img_url = data.vehicle_other_img_url
                    } else {
                        templateAlertContainer('vehicle_img_collection-holder', 'no-data', "No image collection added", null, '<i class="fa-solid fa-image"></i>', null)
                    }

                    if(data.vehicle_document){
                        $('#doc_attachment-holder').empty()
                        data.vehicle_document.forEach(dt => {
                            let preview = ""

                            if (dt.vehicle_document_type === "image") {
                                preview = `<img src="${dt.vehicle_document_url}" class="img img-fluid my-2"style="max-width: 200px;">`
                            } else if (dt.vehicle_document_type === "pdf") {
                                preview = `<iframe src="${dt.vehicle_document_url}" style="width: 200px; height: 200px;" class="my-2"></iframe>`
                            } 

                            $('#doc_attachment-holder').append(`
                                <div class="col-xl-6 col-lg-12 col-md-6 col-sm-12">
                                    <div class="container-fluid my-3 text-center">
                                        ${preview}
                                        <p class="mt-1"><b>Caption:</b> ${dt.vehicle_document_caption ?? "-"}</p>
                                        <a class="btn btn-danger btn-delete w-100" style="width:50px;" data-url="/api/v1/vehicle/document/destroy/<?= $id ?>/${dt.vehicle_document_id}" data-context="Vehicle Document"><i class="fa-solid fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            `)
                        });
                        
                    }

                    $('#created_at').text(data.created_at)
                    $('#updated_at').text(data.updated_at ?? '-')
                    $('#deleted_at').text(data.deleted_at ?? '-')
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        generateApiError(response, true)
                    } else {
                        failedRoute('vehicle','/garage')
                    }
                }
            });
        }
        get_vehicle('<?= $id ?>')
    </script>
@endsection