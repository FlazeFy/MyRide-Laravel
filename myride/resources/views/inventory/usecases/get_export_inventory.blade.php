<a class="btn btn-success ms-1" id="export_excel"><i class="fa-solid fa-download"></i> Export Excel</a>

<script>
    $(document).on('click','#export_excel',function(){
        const get_export_inventory = () => {
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/export/inventory`,
                type: 'GET',
                xhrFields: { responseType: 'blob' },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get('token_key'); ?>")
                },
                success: function(response, status, xhr) {
                    Swal.close()
                    
                    let fileName = "inventory.xlsx"
                    const disposition = xhr.getResponseHeader('Content-Disposition')
                    if (disposition && disposition.includes('filename=')) {
                        fileName = disposition.split('filename=')[1].trim().replace(/"/g, '')
                    }

                    const url = window.URL.createObjectURL(response)
                    const link = document.createElement('a')
                    link.href = url
                    link.setAttribute('download', fileName)
                    document.body.appendChild(link)
                    link.click()
                    link.remove()

                    Swal.fire({
                        title: "Success!",
                        text: `Inventory data downloaded`,
                        icon: "success",
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
        get_export_inventory()
    })
</script>