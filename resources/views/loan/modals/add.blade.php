<div class="form-content addLoan" style="display:none;">
    <form id="formAddLoan" >
        <!-- Booking Type -->
        <label id="bookingPeriodLabel">Booking Period</label><br>
        <span id="loanTypeError" class="inputError"></span>
        <label class="radio-inline"><input type="radio" id="loanTypeSingle" name="loanType">Single Day</label>
        <label class="radio-inline"><input type="radio" id="loanTypeMulti" name="loanType">Multiple Days</label><br>

        <div id="singleDayBooking">
            <!-- Loan Date -->
            <label id="loanDateLabel">Date</label>
            <span id="loanDateError" class="inputError"></span>
            <div class="input-group date datetimepicker6" data-target-input="nearest">
                <input id="loanDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker6"/>
                <div class="input-group-append" data-target=".datetimepicker6" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>

            <!-- Loan Start Period -->
            <label id="loanStartTimeLabel">Start Time</label>
            <span id="startTimeError" class="inputError"></span>
            <div class="input-group date datetimepicker9" data-target-input="nearest">
                <input id="loanStartTime" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker9">
                <div class="input-group-append" data-target=".datetimepicker9" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                </div>
            </div>

            <!-- Loan End Period -->
            <label id="loanEndTimeLabel">End Time</label>
            <span id="endTimeError" class="inputError"></span>
            <div class="input-group date datetimepicker10" data-target-input="nearest">
                <input id="loanEndTime" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker10">
                <div class="input-group-append" data-target=".datetimepicker10" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                </div>
            </div>
        </div>

        <div id="multiDayBooking">
            <!-- Loan Start Date -->
            <label id="loanStartDateLabel">Start Date</label>
            <span id="startDateError" class="inputError"></span>
            <div class="input-group date datetimepicker7" data-target-input="nearest">
                <input id="loanStartDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker7"/>
                <div class="input-group-append" data-target=".datetimepicker7" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>

            <!-- Loan End Date -->
            <label id="loanEndDateLabel">End Date</label>
            <span id="endDateError" class="inputError"></span>
            <div class="input-group date datetimepicker8" data-target-input="nearest">
                <input id="loanEndDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker8"/>
                <div class="input-group-append" data-target=".datetimepicker8" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>

        <!-- User Selected -->
        <label id="userSelectedLabel">User</label>
        <span id="userSelectedError" class="inputError"></span>
        <select class="form-control" id="userSelected">
            <option>Please select a user...</option>
            @foreach ($users as $user)
                <option>{{ $user->forename }} {{ $user->surname }}</option>
            @endforeach
        </select>

        <!-- Equipment -->
        <label id="equipmentTableLabel">Equipment</label>
        <span id="equipmentSelectedError" class="inputError"></span>
        <select class="form-control" id="equipmentSelected">
        </select>

        <div id="equipmentList">
            <table class="table" id="equipmentTable">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Remove</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <!-- Additional Details -->
        <label>Additional details</label>
        <span id="additionalDetailsError" class="inputError"></span>
        <textarea class="form-control" id="additionalDetails"></textarea>

        <!-- Reservation -->
        <hr>
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="reservation" value="reserved" id="reservation">
        <span id="reservationError" class="inputError"></span>
        <label class="form-check-label" for="defaultCheck1">
            Reservation
        </label>
        </div>
    </form>
</div>