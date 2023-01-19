<div class="row" >
    <div class="col-lg-12">
        <div class="row">
            <div class="card w-100 mr-3">
                <div class="card-header {{ $incident->status_id == 0 ? 'bg-danger' : 'bg-success' }} text-center">
                    <h1>Incident #{{ $incident->id }}</h1>
                </div>
                <div wire:poll.10s class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>Start Date: </strong><p class="card-text">{{ $incident->start_date_time }}</p>
                            <strong>Status:</strong><p class="card-text">{{ $incident->status }}</p>
                            <strong>Location:</strong><p class="card-text"><x-link route="locations" id="{{ $incident->location->id }}" value="{{ $incident->location->name }}"></x-link></p>
                            <strong>Alert: <x-link route="distributionGroups" id="{{ $incident->group->id }}" value="{{ $incident->group->name }}"></x-link></strong>
                            <ul>
                                @foreach($incident->group->users as $user)
                                    <li>
                                        <x-link route="users" id="{{ $user->id }}" value="{{ $user->forename }} {{ $user->surname }}"></x-link>
                                    </li>
                                @endforeach
                            </ul>
                            <strong>Details:</strong><p class="card-text">{{ $incident->details }}</p>
                            <strong>Created Date:</strong><p class="card-text">{{ $incident->humanFormat($incident->created_at) }}</p>
                            <strong>Last Updated:</strong><p class="card-text">{{ $incident->humanFormat($incident->updated_at) }}</p>
                            <strong>Created By:</strong><p class="card-text">{{ $incident->user_created_by->forename }} {{ $incident->user_created_by->surname }}</p>
                        </div>

                        <div class="col-6">
                            <strong>Issues:</strong>
                            <p class="card-text">
                                <ul>
                                    @foreach($incident->issues as $issue)
                                        <li>
                                            <x-link route="equipmentIssues" id="{{ $issue->id }}" value="x{{ $issue->pivot->quantity }} {{ $issue->title }} (£{{ $issue->cost }})"></x-link>
                                        </li>
                                    @endforeach
                                </ul>
                                <strong>Total Cost: </strong><p class="card-text">TODO</p>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>