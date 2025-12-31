<h2>All History</h2><hr>
<div id="history-holder"></div>

<script>
    let page = 1

    const getAllHistory = (page) => {
        const holder = 'history-holder'

        $.ajax({
            url: `/api/v1/history?page=${page}`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                const current_page = response.data.current_page
                const total_page = response.data.last_page
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <div class="container-fluid p-3">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <div class="chip bg-info mb-0 mx-0 d-inline-block">${dt.history_type}</div>
                                <h6 class='mb-1'>You have ${dt.history_context}</h6>
                            </div>
                            <p class='date-text text-secondary mb-2'>Created At : ${getDateToContext(dt.created_at,'calendar')}</p>
                            <a class="btn btn-danger btn-delete small" data-url="/api/v1/history/destroy/${dt.id}" data-context="History"><i class="fa-solid fa-trash"></i> Delete</a>
                        </div>
                    `)
                });

                generatePagination(holder, getAllHistory, total_page, current_page)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    templateAlertContainer(holder, 'no-data', "No history found", null, '<i class="fa-solid fa-rotate-left"></i>',null)
                }
            }
        });
    };
    getAllHistory(page)
</script>