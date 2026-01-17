<style>
    #wash_tb tbody h6, #wash_tb tbody p {
        margin-bottom:0;
    }
</style>

<h2 class="mb-3">Wash History</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 240px;">Washing Info & Detail</th>
                <th scope="col" style="min-width: 160px;">Time</th>
            </tr>
        </thead>
        <tbody id="wash_tb"></tbody>
    </table>
</div>

<script>
    const generatePaginationWash = (items_holder, fetch_callback, total_page, current_page, page_trip, id) => {
        let page_element = ''
        for (let i = 1; i <= total_page; i++) {
            page_element += `
                <a class='btn-page ${i === current_page ? 'active' : ''}' href='#' data-page='${i}' title='Open page: ${i}'>${i}</a>
            `
        }

        $(`#pagination-${items_holder}`).remove()
        $(`<div id='pagination-${items_holder}' class='btn-page-holder'><label>Page</label>${page_element}</div>`).insertAfter($(`#${items_holder}`).closest('table'))

        $(document).off('click', `#pagination-${items_holder} .btn-page`)
        $(document).on('click', `#pagination-${items_holder} .btn-page`, function() {
            const selectedPage = $(this).data('page')
            fetch_callback(id, page_trip, selectedPage,false)
        });

        $(`#${items_holder}`).closest('table').css('margin-bottom', '0')
    }

    const build_layout_wash = (data,fetch_callback,page_trip,id) => {
        const holder = 'wash_tb'
        $(`#${holder}`).empty()

        if(data){
            const current_page = data.current_page
            const total_page = data.last_page

            data.data.forEach((dt, idx) => {
                $(`#${holder}`).append(`
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
                                        dt[wash.key] ? `<span style='font-size: var(--textMD);'>${wash.label}</span>, ` : ''
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

            generatePaginationWash(holder, fetch_callback, total_page, current_page, page_trip, id)
        } else {
            $(`#${holder}`).html(`
                <tr>
                    <td colspan="3"><p class="m-0 fst-italic text-secondary">- No Wash History -</p></td>
                </tr>
            `)
        }
    }
</script>