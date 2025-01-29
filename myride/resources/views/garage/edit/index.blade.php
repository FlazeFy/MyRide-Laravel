@extends('layouts.main_layout')

@section('content')
    <div class="d-block mx-auto p-3">
        <div class="d-flex justify-content-between">
            <button class="btn btn-nav-page" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
            @include('garage.edit.usecases.get_props')
        </div>
        <h2>Edit Vehicle</h2><br>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('garage.edit.usecases.put_vehicle_data')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('garage.edit.usecases.put_vehicle_doc')
            </div>
        </div>
    </div>


<script>
    const get_vehicle = (id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/detail/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
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

                $('#created_at').text(data.created_at)
                $('#updated_at').text(data.updated_at ?? '-')
                $('#deleted_at').text(data.deleted_at ?? '-')
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
    }
    get_vehicle('<?= $id ?>')
</script>

<script src='https://www.gstatic.com/firebasejs/6.0.2/firebase.js'></script>
<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAziQMCG6NEKuLhFp9AyzavVPRMdJwT5uw",
        authDomain: "myride-a0077.firebaseapp.com",
        projectId: "myride-a0077",
        storageBucket: "myride-a0077.appspot.com",
        messagingSenderId: "868020179967",
        appId: "1:868020179967:web:0dccb0551a6faeeb810dca",
        measurementId: "G-GTZV92C8MK"
    }
    firebase.initializeApp(firebaseConfig)
</script>
@endsection