const message_short_image = (target, image, caption) => {
    $(`#${target}`).html(`
        <div class="message small">
            <img src="${image}" class="img img-fluid">
            <p class="text-secondary">${caption}</p>
        </div>
    `)
}

const failedMsg = (context) => {
    Swal.fire({
        title: "Oops!",
        text: `Failed to ${context}`,
        icon: "error"
    });
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
    });
}