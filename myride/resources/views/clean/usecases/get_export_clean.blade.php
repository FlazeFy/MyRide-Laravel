<a class="btn btn-success" id="export_excel"><i class="fa-solid fa-download"></i> Export Excel</a>

<script>
    $(document).on('click','#export_excel',function(){
        get_export('clean',token)
    })
</script>