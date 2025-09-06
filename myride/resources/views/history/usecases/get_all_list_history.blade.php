<h2>All History</h2>
<div id="history-holder"></div>

<script>
    let page = 1

    const get_all_history = (page) => {
        const holder = $('#history-holder')

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/history`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                holder.empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
                data.forEach(dt => {
                    holder.append(`
                        <div class="container bordered p-3">
                            <h6 class='mb-1'>${dt.history_type} ${dt.history_context}</h6>
                            <p class='date-text text-secondary'>Created At : ${getDateToContext(dt.created_at,'calendar')}</p>
                            <a class="btn btn-danger btn-delete small" data-url="/api/v1/history/destroy/${dt.id}" data-context="History"><i class="fa-solid fa-trash"></i> Delete</a>
                        </div>
                    `)
                });
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
    };
    get_all_history(page)
</script>