<div wire:poll.1s>
    <!-- Headings -->
    <div class="row">
        <div class="col-6">
            <h1 class="text-center" style="color:white">Inventory Booking System</h1>
        </div>
        <div  class="col-6">
            <h1 class="text-center" style="color:white">{{ now()->format('l F jS Y H:m:s') }}</h1>
        </div>
    </div>

    <!-- Content -->
    <div class="row">
        <div class="col-6">
            @foreach ($loans as $loan)
                @if ($loan->status_id == 0 or $loan->status_id == 1 or $loan->status_id == 2)
                    @switch($loan->status_id)
                        @case(0)
                            <div class="card bg-success w-100">
                            @break
                        @case(1)
                            <div class="card bg-warning w-100">
                            @break
                        @case(2)
                            <div class="card bg-danger w-100">
                            @break
                    @endswitch
                        <div class="card-header text-center">Loan #{{ $loan->id }} : {{ $loan->user->forename }} {{ $loan->user->surname }} : {{ $loan->start_date_time }} </div>
                        <div class="card-body p-1 ">
                            <div class="row">
                                <div class="col-12 text-center">
                                    {{ $loan->details }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <ul style="list-style-type: none;" class="text-center">
                                        @for ($i = 0; $i < $loan->assets->count() / 2; $i++)
                                            @if ($loan->assets[$i]->pivot->returned == 1)
                                                <li style="text-decoration: line-through;">{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @else
                                                <li>{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul style="list-style-type: none;" class="text-center">
                                        @for ($i = $loan->assets->count() / 2; $i < $loan->assets->count(); $i++)
                                            @if ($loan->assets[$i]->pivot->returned == 1)
                                                <li style="text-decoration: line-through;">{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @else
                                                <li>{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="col-6">
            @foreach ($loans as $loan)
                @if ($loan->status_id == 3)
                    <div class="card bg-secondary w-100">
                        <div class="card-header text-center">Loan #{{ $loan->id }} : {{ $loan->user->forename }} {{ $loan->user->surname }} : {{ $loan->start_date_time }} </div>
                        <div class="card-body p-1 ">
                            <div class="row">
                                <div class="col-12 text-center">
                                    {{ $loan->details }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <ul style="list-style-type: none;" class="text-center">
                                        @for ($i = 0; $i < $loan->assets->count() / 2; $i++)
                                            @if ($loan->assets[$i]->pivot->returned == 1)
                                                <li style="text-decoration: line-through;">{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @else
                                                <li>{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul style="list-style-type: none;" class="text-center">
                                        @for ($i = $loan->assets->count() / 2; $i < $loan->assets->count(); $i++)
                                            @if ($loan->assets[$i]->pivot->returned == 1)
                                                <li style="text-decoration: line-through;">{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @else
                                                <li>{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})</li>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-6">
            <div class="card bg-danger w-100">
                <div class="card-header text-center">Loan #1 John Smith 10:25</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card bg-secondary w-100">
                <div class="card-header text-center">Setup #1 : Mary Johnson (Gym Assmbly) : Ranmore Gym : 13:45 </div>
                <div class="card-body">
                  <p class="card-text">Please can sound and projector be set up for Y8 assembly today</p>
                  <p class="card-text">
                    <ul>
                        <li>Projector</li>
                        <li>PA System</li>
                    </ul>
                  </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card bg-success w-100">
                <div class="card-header text-center">Loan #1 John Smith 10:25</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card bg-secondary w-100">
                <div class="card-header">Header</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card bg-success w-100">
                <div class="card-header text-center">Loan #1 John Smith 10:25</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card bg-secondary w-100">
                <div class="card-header">Header</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card bg-danger w-100">
                <div class="card-header text-center">Loan #1 John Smith 10:25</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card bg-secondary w-100">
                <div class="card-header">Header</div>
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>
    </div>
</div> --}}
