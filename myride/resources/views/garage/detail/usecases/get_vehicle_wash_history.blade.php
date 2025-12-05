<style>
    #wash_tb tbody h6, #wash_tb tbody p {
        font-size:var(--textSM) !important;
        margin-bottom:0;
    }
</style>

<h2 class="mb-3">Wash History</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered" id="wash_tb">
        <thead>
            <tr>
                <th scope="col" style="min-width: 240px;">Washing Info & Detail</th>
                <th scope="col" style="min-width: 160px;">Time</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    const build_layout_wash = (data) => {
        $('#wash_tb tbody').empty()
        if(data){
            data.data.forEach((dt, idx) => {
                $('#wash_tb tbody').append(`
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="mb-0">Wash By</h6>
                                    <p>${dt.wash_by ?? '-'}</p>
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-0">Address</h6>
                                    <p>${dt.wash_address ?? '-'}</p>
                                </div>
                            </div>
                            <h6 class="mb-0">Description</h6>
                            <p>${dt.wash_desc ?? '-'}</p>
                            <h6 class="mb-0">Tools</h6>
                            <p>${dt.wash_tools ?? '-'}</p>
                            <hr>
                            <div class="row">
                                <h6 class="mb-0">Wash Detail</h6>
                                <p class='mb-0'>
                                    ${[
                                        { key: "is_wash_body", label: "Body Washing" },
                                        { key: "is_wash_window", label: "Window Washing" },
                                        { key: "is_wash_dashboard", label: "Dashboard Washing" },
                                        { key: "is_wash_tires", label: "Tires Washing" },
                                        { key: "is_wash_trash", label: "Trash Washing" },
                                        { key: "is_wash_engine", label: "Engine Washing" },
                                        { key: "is_wash_seat", label: "Seat Washing" },
                                        { key: "is_wash_carpet", label: "Carpet Washing" },
                                        { key: "is_wash_pillows", label: "Pillow Washing" },
                                        { key: "is_wash_hollow", label: "Vehicle Hollow Washing" },
                                        { key: "is_fill_window_washing_water", label: "Window Washing Water Fill" },
                                    ].map(wash => 
                                        dt[wash.key] ? `<span style='font-size:var(--textMD);'>${wash.label}</span>, ` : ''
                                    ).join('')}
                                </p>
                            </div>
                        </td>
                        <td>
                            <h6 class="mb-0">Start At</h6>
                            <p>${dt.wash_start_time}</p>
                            <h6 class="mb-0">Finished At</h6>
                            <p class="mb-0">${dt.wash_end_time ?? "In Progress"}</p>
                        </td> 
                    </tr>
                `)
            });
        } else {
            $('#wash_tb tbody').html(`
                <tr>
                    <td colspan="3"><p class="m-0 fst-italic text-secondary">- No Wash History -</p></td>
                </tr>
            `)
        }
    }
</script>