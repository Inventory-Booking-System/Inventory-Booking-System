@extends('layouts.app')

@section('mainContent')
    <style>
        .addStrike {
            text-decoration: line-through;
        }
    </style>

    <form action="/loans/{{ $loan->id }}" method="POST" enctype="multipart/form-data" >
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Start Date/Time, User, Equipment Selection, Additional Details, Reservation -->
            <div class="col-lg-5 offset-lg-2 p-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <!-- Loan Start Date -->
                        <label id="loanStartDateLabel">Start Date</label>
                        @if($errors->has('start_date'))
                            <span class="text-danger">{{ $errors->first('start_date') }}</span>
                        @endif
                        <div class="input-group date dtpStartDateTime" data-target-input="nearest">
                            <input name="start_date" id="loanStartDate" type="text" value="{{ old('start_date', $loan->start_date_time) }}" class="form-control datetimepicker-input" data-target=".dtpStartDateTime"/>
                            <div class="input-group-append" data-target=".dtpStartDateTime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <!-- Loan End Date -->
                        <label id="loanEndDateLabel">End Date</label>
                        @if($errors->has('end_date'))
                            <span class="text-danger">{{ $errors->first('end_date') }}</span>
                        @endif
                        <div class="input-group date dtpEndDateTime" data-target-input="nearest">
                            <input name="end_date" id="loanEndDate" type="text" value="{{ old('end_date', $loan->end_date_time) }}" class="form-control datetimepicker-input" data-target=".dtpEndDateTime"/>
                            <div class="input-group-append" data-target=".dtpEndDateTime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Selected -->
                <label id="userSelectedLabel">User</label>
                @if($errors->has('user_id'))
                    <span class="text-danger">{{ $errors->first('user_id') }}</span>
                @endif
                <select name="user_id" class="form-control" id="userSelected"">
                    <option></option>
                    @foreach ($users as $user)
                        @if (old('user_id', $loan->user->id) == $user->id)
                            <option value="{{ $user->id }}" selected>{{ $user->forename }} {{ $user->surname }}</option>
                        @else
                            <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
                        @endif
                    @endforeach
                </select>

                <!-- Equipment -->
                <label class="mt-3" id="equipmentTableLabel">Equipment</label>
                @if($errors->has('equipmentSelected'))
                    <span class="text-danger">{{ $errors->first('equipmentSelected') }}</span>
                @endif
                <select class="form-control" id="equipmentSelected">
                </select>

                <!-- Additional Details -->
                <label class="mt-3">Additional details</label>
                @if($errors->has('details'))
                    <p class="text-danger">{{ $errors->first('details') }}</p>
                @endif
                <textarea rows="8" name="details" class="form-control" id="additionalDetails">{{ old('details', $loan->details) }}</textarea>

                <!-- Reservation -->
                <label class="mt-3">Reservation</label>
                @if($errors->has('status_id'))
                    <span class="text-danger">{{ $errors->first('status_id') }}</>
                @endif
                <br>
                <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                    <label class="btn btn-success">
                      <input type="radio" name="status_id" value="1" id="option1" @if(old('status_id', $loan->status_id) == "1") checked @endif> Yes
                    </label>
                    <label class="btn btn-success">
                      <input type="radio" name="status_id" value="0" id="option2" @if(old('status_id') == $loan->status_id) checked @endif> No
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Modify Loan</button>
            </div>

            <!-- Shopping Cart -->
            <div class="col-lg-3 p-3">
                <div id="equipmentList">
                    <table class="table" id="equipmentTable">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Remove</th>
                                <th scope="col">Book In</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset(session()->getOldInput()['bookableEquipment']))
                                @foreach (old('bookableEquipment') as $equipment)
                                    @isset($equipment->selected)
                                        @if($equipment->selected)
                                            <tr id="{{ $equipment->id }}" data-returned="{{ $equipment->pivot->returned }}">
                                                <td>{{ $equipment->name }} ({{ $equipment->tag }})</td>
                                                <td><button class="removeFromCart btn btn-danger btn-sm rounded-0" type="button" data-assetname="{{ $equipment->name }} ({{ $equipment->tag }})" data-assetid="{{ $equipment->id }}" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button></td>
                                                <td>
                                                    @if($equipment->pivot->returned == 0)
                                                        <button class="bookFromCart btn btn-success btn-sm rounded-0" type="button" data-assetname="{{ $equipment->name }} ({{ $equipment->tag }})" data-assetid="{{ $equipment->id }}" data-toggle="tooltip" data-placement="top" title="Book In Single"><i class="fa fa-check"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endisset
                                @endforeach
                            @else
                                <!-- Get posted equipment data -->
                                @foreach($loan->assets as $equipment)
                                    <tr id="{{ $equipment->id }}" data-returned="{{ $equipment->pivot->returned }}">
                                        <td>{{ $equipment->name }} ({{ $equipment->tag }})</td>
                                        <td><button class="removeFromCart btn btn-danger btn-sm rounded-0" type="button" data-assetname="{{ $equipment->name }} ({{ $equipment->tag }})" data-assetid="{{ $equipment->id }}" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button></td>
                                        <td>
                                            @if($equipment->pivot->returned == 0)
                                                <button class="bookFromCart btn btn-success btn-sm rounded-0" type="button" data-assetname="{{ $equipment->name }} ({{ $equipment->tag }})" data-assetid="{{ $equipment->id }}" data-toggle="tooltip" data-placement="top" title="Book In Single"><i class="fa fa-check"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="equipmentToSubmit" name="equipmentSelected">
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/loans.js') }}"></script>
@endpush