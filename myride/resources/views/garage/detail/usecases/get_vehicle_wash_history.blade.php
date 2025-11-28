<style>
    #clean_tb tbody h6, #clean_tb tbody p {
        font-size:var(--textSM) !important;
        margin-bottom:0;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered" id="clean_tb">
        <thead>
            <tr>
                <th scope="col">Cleaning Info & Detail</th>
                <th scope="col" style="width: 200px;">Time</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    const build_layout_clean = (data) => {
        $('#clean_tb tbody').empty()
        if(data){
            data.data.forEach((dt, idx) => {
                $('#clean_tb tbody').append(`
                    <tr>
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
                            <hr>
                            <div class="row">
                                <h6 class="mb-0">Clean Detail</h6>
                                <p>
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
                                    ].map(clean => 
                                        dt[clean.key] ? `<span style='font-size:var(--textXMD);'>${clean.label}</span>, ` : ''
                                    ).join('')}
                                </p>
                            </div>
                        </td>
                        <td>
                            <h6 class="mb-0">Start At</h6>
                            <p>${dt.clean_start_time}</p>
                            <h6 class="mb-0">Finished At</h6>
                            <p class="mb-0">${dt.clean_end_time ?? "In Progress"}</p>
                        </td> 
                    </tr>
                `)
            });
        } else {
            $('#clean_tb tbody').html(`
                <tr>
                    <td colspan="3"><p class="m-0 fst-italic text-secondary">- No Wash History -</p></td>
                </tr>
            `)
        }
    }
</script>