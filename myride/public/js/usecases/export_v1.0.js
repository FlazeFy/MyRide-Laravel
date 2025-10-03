const get_export = (ctx, token) => {
    Swal.showLoading()
    $.ajax({
        url: `/api/v1/export/${ctx}`,
        type: 'GET',
        xhrFields: { responseType: 'blob' },
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Accept", "application/json")
            xhr.setRequestHeader("Authorization", `Bearer ${token}`)
        },
        success: function(response, status, xhr) {
            Swal.close()
            
            let fileName = `${ctx}.xlsx`
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
                text: `${ctx} data downloaded`,
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