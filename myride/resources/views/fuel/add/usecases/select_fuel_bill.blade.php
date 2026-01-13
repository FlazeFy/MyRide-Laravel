<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Fuel Bill</label>
    <div class="d-flex gap-2" id="fuel_bill_button-holder">
        <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-receipt"></i><span class="d-none d-md-inline"> Add Receipt</span></a>
    </div>
</div>
<div id="fuel_bill-holder"></div>

<script>
    templateAlertContainer('fuel_bill-holder', 'no-data', "No fuel bill selected", null, '<i class="fa-solid fa-receipt"></i>', null)

    $(document).ready(function() {
        $(document).on('click', '#clear_bill-button', function(){
            $('#fuel_bill_button-holder').find(this).remove()
            templateAlertContainer('fuel_bill-holder', 'no-data', "No fuel bill selected", null, '<i class="fa-solid fa-receipt"></i>', null)
        })

        $(document).on('click', '#add_image-button', function () {
            $("#fuel_bill-holder .alert-container").remove()

            if ($("#fuel_bill-holder .fuel_bill-holder").length > 0) {
                Swal.fire("Error!", "You can only add one image as bill", "error")
                return
            }
            if($('#fuel_bill_button-holder').find('#clear_bill-button').length === 0){
                $('#fuel_bill_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_bill-button"><i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span></a>
                `)
            }

            $("#fuel_bill-holder").append(`
                <div class="container-fluid fuel_bill-holder mt-2">
                    <input type="file" id="fuel_bill" class="form-control" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-1 d-none" style="max-width: 200px;">
                </div>
            `)
        })

        $(document).on('change', '#fuel_bill', function(e) {
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