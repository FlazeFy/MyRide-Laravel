const messageShortImage = (target, image, caption) => {
    $(`#${target}`).html(`
        <div class="message small">
            <img src="${image}" class="img img-fluid">
            <p class="text-secondary">${caption}</p>
        </div>
    `)
}

const failedMsg = (context) => {
    Swal.fire("Oops!", `Failed to ${context}`, "error")
}

const messageAlertBox = (holder, type, message) => {
    $(`#${holder}`).html(`
        <div class="container-fluid bg-${type}">
            <h6><i class="fa-solid ${type === 'danger' || type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-info'}"></i> 
            ${type === 'danger' ? ' Alert' : ucFirst(type)}</h6>
            <p class="mb-0">${message}</p>
        </div>
    `)
}

const failedRoute = (context,url_home) => {
    Swal.fire({
        title: "Failed to see detail",
        text: `Do you want to keep open this "${context}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "To home page",
        cancelButtonText: "No, try again",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url_home
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.reload()
        }
    })
}