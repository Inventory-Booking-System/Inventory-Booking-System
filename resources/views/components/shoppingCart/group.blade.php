<div>
    <section>
        <div class="container">
              <div class="row d-flex justify-content-center align-items-center">
                <div class="col">
                      <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h5 class="mb-0 text-right">@isset($totalCost)<a href="#" class="text-body font-weight-bold">Total Cost <span class="font-weight-normal">{{ $totalCost }}</span></a>@endisset</h5>
                                    <hr>
                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>