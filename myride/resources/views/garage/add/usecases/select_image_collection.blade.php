<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Vehicle Image Collection</label>
    <div class="d-flex gap-2" id="vehicle_img_collection_button-holder">
        <a class="btn btn-primary py-1" id="add_image_collection-button">
            <i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span>
        </a>
    </div>
</div>
<div id="vehicle_img_collection-holder" class="row mx-1"></div>

<script>
    templateAlertContainer('vehicle_img_collection-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)

    $(document).ready(function() {
        $(document).on('click', '#clear_attachment-button', function(){
            $('#vehicle_img_collection_button-holder').find(this).remove()
            $('#vehicle_img_collection-holder').empty()
            templateAlertContainer('vehicle_img_collection-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
        })

        $(document).on('click', '#add_image_collection-button', function () {
            $("#vehicle_img_collection-holder .alert-container").remove()

            if ($("#vehicle_img_collection-holder .vehicle-image-holder").length > 9) {
                Swal.fire("Error!", "You can only add 10 image inputs for vehicle album", "error")
                return
            }
            if($('#vehicle_img_collection_button-holder').find('#clear_attachment-button').length === 0){
                $('#vehicle_img_collection_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_attachment-button">
                        <i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span>
                    </a>
                `)
            }

            $("#vehicle_img_collection-holder").append(`
                <div class="col-md-6 col-sm-12">
                    <div class="container-fluid vehicle-image-holder mt-2">
                        <input type="file" class="vehicle_other_images form-control" accept="image/jpeg,image/png,image/gif"><br>
                        <img class="image-preview mt-1 d-none" style="max-width: 200px">
                    </div>
                </div>
            `)
        })

        $(document).on('change', '.vehicle_other_images', function(e) {
            const file = e.target.files[0]
            const $preview = $(this).siblings('.image-preview')

            if (!file) return
            const maxSize = 5 * 1024 * 1024
            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')
                $(this).val('')
                $preview.addClass('d-none').attr('src', '')
                return
            }

            const reader = new FileReader()
            reader.onload = function (event) {
                $preview.attr('src', event.target.result).removeClass('d-none')
            }
            reader.readAsDataURL(file)
        })
    })
</script>
