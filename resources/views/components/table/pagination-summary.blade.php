
<div class="row mt-2">
    <div class="col-lg-3 d-flex flex-row">
        <span>Showing {{ $model->total() > 0 ? ($model->currentPage() * $model->count()) - ($model->count() - 1) : 0 }} to {{ $model->currentPage() * $model->count() }} of {{ $model->total() }} results</span>
    </div>
    <div class="col-lg-9 d-flex flex-row-reverse">
        {{ $model->links() }}
    </div>
</div>