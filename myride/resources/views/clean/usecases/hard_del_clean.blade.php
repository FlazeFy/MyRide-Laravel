<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete-{{$dt->id}}"><i class="fa-solid fa-trash"></i></button>
<div class="modal fade" id="modalDelete-{{$dt->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Warning</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="/clean/destroy/{{$dt->id}}" method="POST" id="form-sign-out">
                    @csrf
                    <p>Are you sure want to delete this item?</p>
                    <button class="btn btn-danger mt-4" type="submit">Yes, Permentally Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>