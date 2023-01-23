<div class="row" >
    <div class="col-lg-12">
        <div class="row">
            <div class="card w-100 mr-3">
                <div class="card-header bg-primary text-center">
                    <h1>Setup #{{ $setup->id }} {{ $setup->loan->user->forename }} {{ $setup->loan->user->surname }}</h1>
                </div>
                <div wire:poll.10s class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>Title: </strong><p class="card-text">{{ $setup->title }}</p>
                            <strong>Location: </strong><p class="card-text">{{ $setup->location->name }}</p>
                            <strong>Start Date: </strong><p class="card-text">{{ $setup->loan->start_date_time }}</p>
                            <strong>End Date:</strong><p class="card-text">{{ $setup->loan->end_date_time }}</p>
                            <strong>Details:</strong><p class="card-text">{{ $setup->loan->details }}</p>
                            <strong>Created Date:</strong><p class="card-text">{{ $setup->loan->humanFormat($setup->loan->created_at) }}</p>
                            <strong>Last Updated:</strong><p class="card-text">{{ $setup->loan->humanFormat($setup->loan->updated_at) }}</p>
                            <strong>Created By:</strong><p class="card-text">{{ $setup->loan->user_created_by->forename }} {{ $setup->loan->user_created_by->surname }}</p>
                        </div>

                        <div class="col-6">
                            <strong>Assets Out:</strong>
                            <p class="card-text">
                                <ul>
                                    @foreach($setup->loan->assets as $asset)
                                        @if (!$asset->pivot->returned)
                                            <li>
                                                <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})"></x-link>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </p>
                            <strong>Assets {{ $setup->loan->status_id == 1 ? 'Reserved' : 'Returned' }}:</strong>
                            <p class="card-text">
                                <ul>
                                    @foreach($setup->loan->assets as $asset)
                                        @if ($asset->pivot->returned)
                                            <li>
                                                <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})"></x-link>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>