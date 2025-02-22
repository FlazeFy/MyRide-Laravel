<div class="mx-4">
    <h2>All Clean History</h2><br>
    @include('clean.usecases.get_export_clean')
    <table class="table table-bordered" id="clean_tb">
        <thead>
            <tr>
                <th scope="col">Vehicle Name</th>
                <th scope="col">Cleaning Info</th>
                <th scope="col">Cleaning Detail</th>
                <th scope="col">Time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        const get_all_vehicle = (page, limit, callback) => {
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/clean`,
                type: 'GET',
                data: { 
                    page: page, 
                    limit: limit
                }, 
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get('token_key'); ?>")
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data.data
                    const total = response.data.total

                    $('#clean_tb tbody').empty()
                    data.forEach((dt, idx) => {
                        $('#clean_tb tbody').append(`
                            <tr>
                                <td>
                                    <h6 class="mb-0">${dt.vehicle_name}</h6>
                                    <p>${dt.vehicle_plate_number}</p>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6 class="mb-0">Clean By</h6>
                                            <p>${dt.clean_by ?? '-'}</p>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-0">Address</h6>
                                            <p>${dt.clean_address ?? '-'}</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">Description</h6>
                                    <p>${dt.clean_desc ?? '-'}</p>
                                    <h6 class="mb-0">Tools</h6>
                                    <p>${dt.clean_tools ?? '-'}</p>
                                </td>
                                <td style="max-width:var(--tcolMinLG);">
                                    <div class="row">
                                        ${[
                                            { key: "is_clean_body", label: "Body Cleaning" },
                                            { key: "is_clean_window", label: "Window Cleaning" },
                                            { key: "is_clean_dashboard", label: "Dashboard Cleaning" },
                                            { key: "is_clean_tires", label: "Tires Cleaning" },
                                            { key: "is_clean_trash", label: "Trash Cleaning" },
                                            { key: "is_clean_engine", label: "Engine Cleaning" },
                                            { key: "is_clean_seat", label: "Seat Cleaning" },
                                            { key: "is_clean_carpet", label: "Carpet Cleaning" },
                                            { key: "is_clean_pillows", label: "Pillow Cleaning" },
                                            { key: "is_clean_hollow", label: "Vehicle Hollow Cleaning" },
                                            { key: "is_fill_window_cleaning_water", label: "Window Cleaning Water Fill" },
                                            { key: "is_fill_fuel", label: "Fuel Fill" }
                                        ].map(clean => `
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" ${dt[clean.key] ? 'checked' : ''}>
                                                    <label class="form-check-label">${clean.label}</label>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0">Start At</h6>
                                    <p>${dt.clean_start_time}</p>
                                    <h6 class="mb-0">Finished At</h6>
                                    <p>${dt.clean_end_time ?? "In Progress"}</p>
                                </td>
                                <td>
                                    <button class="btn btn-danger">Delete</button>
                                    ${dt.clean_end_time == null ? `<button class="btn btn-success">Finish Cleaning</button>` : ""}
                                </td>
                            </tr>
                        `)
                    });

                    callback({
                        draw: settings.iDraw,
                        recordsTotal: total,
                        recordsFiltered: total,
                        data: data
                    });
                },
                error: function() {
                    Swal.close()
                    Swal.fire({
                        title: "Oops!",
                        text: "Something went wrong",
                        icon: "error"
                    });
                }
            });
        }

        const table = $('#clean_tb').DataTable({
            pageLength: 14, 
            lengthMenu: [ 
                [14, 28, 75, 125],
                [14, 28, 75, 125] 
            ],
            serverSide: true,  
            ajax: function(data, callback, settings) {
                let page = settings.page + 1
                let limit = settings._iDisplayLength
                get_all_vehicle(page, limit, callback)
            }
        });
    });
</script>
