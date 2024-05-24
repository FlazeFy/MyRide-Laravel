<form action="/garage/edit/{{$dt_vehicle->id}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Name</label>
            <div class="input-group mb-3">
                @php($carnametransmission = explode(" ", $dt_vehicle->vehicle_name))
                @php($transmission = $carnametransmission[count($carnametransmission)-1])
                @php($carname = array_slice($carnametransmission, 0, count($carnametransmission) - 1))
                @php($carname = implode(' ', $carname))
                @php($transmission = $carnametransmission[count($carnametransmission)-1])

                <input class="form-control" type="text" value="{{$carname}}" name="vehicle_name" required>
                <select class="form-select" style="width: 20px;" aria-label="Default select example" name="vehicle_transmission_code">
                    <option value="MT" <?= $transmission == "MT" ? "selected" : "" ?>>Manual Transmission</option>
                    <option value="AT" <?= $transmission == "AT" ? "selected" : "" ?>>Automatic Transmission</option>
                    <option value="CVT" <?= $transmission == "CVT" ? "selected" : "" ?>>CVT Transmission</option>
                </select>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Merk</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_merk">
                <option value="{{$dt_vehicle->vehicle_merk}}" selected>{{$dt_vehicle->vehicle_merk}}</option>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Type</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_type">
                <option value="{{$dt_vehicle->vehicle_type}}" selected>{{$dt_vehicle->vehicle_type}}</option>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Price</label>
            <div class="input-group mb-3">
                <span class="input-group-text">Rp. </span>
                <input class="form-control" type="number" name="vehicle_price" value="{{$dt_vehicle->vehicle_price}}" min="1" required>
                <span class="input-group-text">.00</span>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <label>Description</label>
            <textarea class="form-control" rows="4" value="{{$dt_vehicle->vehicle_desc}}" name="vehicle_desc" required>{{$dt_vehicle->vehicle_desc}}</textarea>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Distance</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" value="{{$dt_vehicle->vehicle_distance}}" name="vehicle_distance" min="1" required>
                <span class="input-group-text">Km</span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Category</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_category" >
                <option value="{{$dt_vehicle->vehicle_category}}" selected>{{$dt_vehicle->vehicle_category}}</option>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_status">
                <option value="{{$dt_vehicle->vehicle_status}}" selected>{{$dt_vehicle->vehicle_status}}</option>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Year Made</label>
            <input class="form-control" type="number" name="vehicle_year_made" value="{{$dt_vehicle->vehicle_year_made}}" min="1000" max="date('Y')" required>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Plate</label>
            <input class="form-control" type="text" name="vehicle_plate_number" value="{{$dt_vehicle->vehicle_plate_number}}" required>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Default Fuel</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_default_fuel">
                <option value="{{$dt_vehicle->vehicle_default_fuel}}" selected>{{$dt_vehicle->vehicle_default_fuel}}</option>
            </select>     
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Fuel Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_fuel_capacity" value="{{$dt_vehicle->vehicle_fuel_capacity}}" min="1" max="100" required>
                <span class="input-group-text">Liter</span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Fuel Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_fuel_status">
                <option value="{{$dt_vehicle->vehicle_fuel_status}}" selected>{{$dt_vehicle->vehicle_fuel_status}}</option>
            </select>    
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Plate</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_color">
                <option value="{{$dt_vehicle->vehicle_color}}" selected>{{$dt_vehicle->vehicle_color}}</option>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Passanger Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" value="{{$dt_vehicle->vehicle_capacity}}" name="vehicle_capacity" min="1" max="100" required>
                <span class="input-group-text">Person</span>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success rounded-pill px-3 py-2"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
</form>
<hr>
<div class="row text-center mt-4">
    <div class="col-lg-4 col-md-6 col-sm-12">
        <h6>Created At</h6>
        <p>{{date('Y-m-d H:i',strtotime($dt_vehicle->created_at))}}</p>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <h6>Updated At</h6>
        @if($dt_vehicle->updated_at)
            <p>{{date('Y-m-d H:i',strtotime($dt_vehicle->updated_at))}}</p>
        @else
            <p>-</p>
        @endif
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <h6>Deleted At</h6>
        @if($dt_vehicle->deleted_at)
            <p>{{date('Y-m-d H:i',strtotime($dt_vehicle->deleted_at))}}</p>
        @else
            <p>-</p>
        @endif
    </div>
</div>
<hr>
