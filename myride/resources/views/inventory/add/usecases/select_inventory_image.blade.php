<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Inventory Image</label>
    <div class="d-flex gap-2" id="inventory_image_button-holder">
        <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
    </div>
</div>
<div id="inventory_image-holder"></div>

<script>
    template_alert_container('inventory_image-holder', 'no-data', "No inventory image selected", null, '<i class="fa-solid fa-image"></i>', null)

    $(document).ready(function() {
        $(document).on('click', '#clear_image-button', function(){
            $('#inventory_image_button-holder').find(this).remove()
            template_alert_container('inventory_image-holder', 'no-data', "No inventory image selected", null, '<i class="fa-solid fa-receipt"></i>', null)
        })

        $(document).on('click', '#add_image-button', function () {
            $("#inventory_image-holder .alert-container").remove()

            if ($("#inventory_image-holder .inventory_image-holder").length > 0) {
                Swal.fire({
                    title: "Error!",
                    text: "You can only add one image as image",
                    icon: "error"
                })
                return
            }
            if($('#inventory_image_button-holder').find('#clear_image-button').length === 0){
                $('#inventory_image_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_image-button"><i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span></a>
                `)
            }

            $("#inventory_image-holder").append(`
                <div class="container-fluid inventory_image-holder mt-2">
                    <input type="file" id="inventory_image" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-2 d-none" style="max-width: 200px;">
                </div>
            `)
        })

        $(document).on('change', '#inventory_image', function(e) {
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