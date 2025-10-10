<table class="table table-bordered" id="driver_tb">
    <thead>
        <tr>
            <th scope="col">Driver</th>
            <th scope="col">Notes</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
    const build_layout_driver = (data) => {
        $('#driver_tb tbody').empty()
        if(data){
            data.forEach((dt, idx) => {
                $('#driver_tb tbody').append(`
                    <tr>
                        <td>
                            <div class="plate-number me-0 mt-2 mb-0 d-inline-block"><i class="fa-solid fa-user-tie"></i> ${dt.username}</div>
                            <p class="text-secondary mb-0 mt-1 fw-bold">${dt.fullname}</p>
                        </td>
                        <td>${dt.notes ?? '<p class="m-0 fst-italic text-secondary">- No Notes Provided -</p>'}</td>
                        <td></td>
                    </tr>
                `)
            });
        } else {
            $('#driver_tb tbody').html(`
                <tr>
                    <td colspan="3"><p class="m-0 fst-italic text-secondary">- No Driver Attached -</p></td>
                </tr>
            `)
        }
    }
</script>