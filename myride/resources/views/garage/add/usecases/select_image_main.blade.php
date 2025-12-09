<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Vehicle Image</label>
    <div class="d-flex gap-2" id="vehicle_image_button-holder">
        <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
    </div>
</div>
<div id="vehicle_img-holder"></div>

<script>
    template_alert_container('vehicle_img-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)

    $(document).ready(function() {
        $(document).on('click', '#clear_attachment-button', function(){
            $('#vehicle_image_button-holder').find(this).remove()
            template_alert_container('vehicle_img-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
        })

        $(document).on('click', '#add_image-button', function () {
            $("#vehicle_img-holder .alert-container").remove()

            if ($("#vehicle_img-holder .vehicle-image-holder").length > 0) {
                Swal.fire({
                    title: "Error!",
                    text: "You can only add one image as attachment",
                    icon: "error"
                })
                return
            }
            if($('#vehicle_image_button-holder').find('#clear_attachment-button').length === 0){
                $('#vehicle_image_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_attachment-button"><i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span></a>
                `)
            }

            $("#vehicle_img-holder").append(`
                <div class="container-fluid vehicle-image-holder mt-2">
                    <input type="file" id="vehicle_image" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-2 d-none" style="max-width: 200px;">
                </div>
            `)
        })

        $(document).on('change', '#vehicle_image', function(e) {
            const file = e.target.files[0]
            if (!file) return

            const maxSize = 5 * 1024 * 1024

            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')

                $(this).val('')
                $('#image-preview').addClass('d-none').attr('src', '')
                return
            }

            const reader = new FileReader()
            reader.onload = function (event) {
                $('#image-preview').attr('src', event.target.result).removeClass('d-none')
            }
            reader.readAsDataURL(file)
        })
    })
</script>