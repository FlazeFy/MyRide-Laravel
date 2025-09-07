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