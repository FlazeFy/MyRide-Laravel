<div class="mx-4">
    <h2 class="text-center">All Clean History</h2>
    <table class="table" id="clean_tb">
        <thead>
            <tr>
                <th scope="col">Vehicle Name</th>
                <th scope="col">Cleaning Info</th>
                <th scope="col">Cleaning Detail</th>
                <th scope="col">Time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dt_all_clean as $dt)
                <tr>
                    <td>
                        <h6 class="mb-0">{{$dt->vehicle_name}}</h6>
                        <p>{{$dt->vehicle_plate_number}}</p>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-6">
                                <h6 class="mb-0">Clean By</h6>
                                @if($dt->clean_by != null)
                                    <p>{{$dt->clean_by}}</p>
                                @else 
                                    <p>-</p>
                                @endif
                            </div>
                            <div class="col-6">
                                <h6 class="mb-0">Address</h6>
                                @if($dt->clean_address != null)
                                    <p>{{$dt->clean_address}}</p>
                                @else 
                                    <p>-</p>
                                @endif
                            </div>
                        </div>

                        <h6 class="mb-0">Description</h6>
                        @if($dt->clean_desc != null)
                            <p>{{$dt->clean_desc}}</p>
                        @else 
                            <p>-</p>
                        @endif

                        <h6 class="mb-0">Tools</h6>
                        @if($dt->clean_tools != null)
                            <p>{{$dt->clean_tools}}</p>
                        @else 
                            <p>-</p>
                        @endif
                    </td>
                    <td style="max-width:var(--tcolMinLG);">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_body == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Body Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_window == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Window Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_dashboard == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Dashboard Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_tires == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Tires Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_trash == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Trash Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_engine == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Engine Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_seat == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Seat Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_carpet == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Carpet Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_pillows == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Pillow Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_clean_hollow == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Vehicle Hollow Cleaning</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_fill_window_cleaning_water == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Window Cleaning Water Fill</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" <?php if($dt->is_fill_fuel == 1) { echo"checked"; } ?>>
                                    <label class="form-check-label" for="flexCheckChecked">Fuel Fill</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <h6 class="mb-0">Start At</h6>
                        <p>{{date("Y-m-d H:i", strtotime($dt->clean_start_time))}}</p>

                        <h6 class="mb-0">Finished At</h6>
                        @if($dt->clean_end_time != null)
                            <p>{{date("Y-m-d H:i", strtotime($dt->clean_end_time))}}</p>
                        @else 
                            <p>In Progress</p>
                        @endif
                    </td>
                    <td>
                        @include('clean.usecases.hard_del_clean')
                        @if($dt->clean_end_time == null)
                            <button class="btn btn-success">Finish Cleaning</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>