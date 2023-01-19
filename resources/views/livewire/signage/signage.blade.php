<div wire:poll.60s style="font-size: 24px;">
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
    <div class="container-fluid">
        <div class="row">
            @for ($i = 0; $i < $loans->count(); $i++)
                    @if ($loans[$i]->status_id == 0 or $loans[$i]->status_id == 1 or $loans[$i]->status_id == 2 or $loans[$i]->status_id == 3)
                        <div class="col-md-4">

                            @switch($loans[$i]->status_id)
                                @case(0)
                                    <div class="card bg-success w-100">
                                    @break
                                @case(1)
                                    <div class="card bg-warning w-100">
                                    @break
                                @case(2)
                                    <div class="card bg-danger w-100">
                                    @break
                                @case(3)
                                    <div class="card bg-secondary w-100">
                                    @break
                            @endswitch
                                <div class="card-header text-center">{{ $loans[$i]->user->forename }} {{ $loans[$i]->user->surname }} : {{ \Carbon\Carbon::parse($loans[$i]->start_date_time)->format('H:i') }} </div>
                                <div class="card-body p-1 ">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            {{ $loans[$i]->details }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <ul style="list-style-type: none;" class="text-center">
                                                @for ($j = 0; $j < $loans[$i]->assets->count(); $j+=2)
                                                    @if ($loans[$i]->assets[$j]->pivot->returned == 1)
                                                        <li style="text-decoration: line-through;">{{ $loans[$i]->assets[$j]->name }} ({{ $loans[$i]->assets[$j]->tag }})</li>
                                                    @else
                                                        <li>{{ $loans[$i]->assets[$j]->name }} ({{ $loans[$i]->assets[$j]->tag }})</li>
                                                    @endif
                                                @endfor
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <ul style="list-style-type: none;" class="text-center">
                                                @for ($j = 1; $j < $loans[$i]->assets->count(); $j+=2)
                                                    @if ($loans[$i]->assets[$j]->pivot->returned == 1)
                                                        <li style="text-decoration: line-through;">{{ $loans[$i]->assets[$j]->name }} ({{ $loans[$i]->assets[$j]->tag }})</li>
                                                    @else
                                                        <li>{{ $loans[$i]->assets[$j]->name }} ({{ $loans[$i]->assets[$j]->tag }})</li>
                                                    @endif
                                                @endfor
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
            @endfor
        </div>
    </div>
</div>