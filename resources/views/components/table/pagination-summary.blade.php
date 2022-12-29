
<div class="row mt-2">
    <div class="col-lg-3 d-flex flex-row">
        <span>Showing {{ ($model->currentPage() * $model->count()) - ($model->count() - 1) }} to {{ $model->currentPage() * $model->count() }} of {{ $model->total() }} results</span>
    </div>
    <div class="col-lg-9 d-flex flex-row-reverse">
        {{ $model->links() }}
    </div>
</div>