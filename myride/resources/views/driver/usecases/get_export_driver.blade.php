<a class="btn btn-success" id="export_excel"><i class="fa-solid fa-download"></i><span class="d-none d-md-inline"> Dataset</span></a>

<script>
    $(document).on('click','#export_excel',function(){
        exportDatasetByModule('driver',token)
    })
</script>