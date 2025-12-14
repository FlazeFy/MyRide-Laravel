<h2>All History</h2><hr>
<div id="history-holder"></div>

<script>
    let page = 1

    const get_all_history = (page) => {
        const holder = 'history-holder'

        $.ajax({
            url: `/api/v1/history`,
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
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <div class="container-fluid p-3">
                            <h6 class='mb-1'>${dt.history_type} ${dt.history_context}</h6>
                            <p class='date-text text-secondary'>Created At : ${getDateToContext(dt.created_at,'calendar')}</p>
                            <a class="btn btn-danger btn-delete small" data-url="/api/v1/history/destroy/${dt.id}" data-context="History"><i class="fa-solid fa-trash"></i> Delete</a>
                        </div>
                    `)
                });
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
    get_all_history(page)
</script>