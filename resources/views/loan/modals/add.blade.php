<div class="form-content addLoan" style="display:none;">
    <form id="formAddLoan" >
        <!-- Loan Start Date -->
        <label id="loanStartDateLabel">Start Date</label>
        <span id="start_dateError" class="inputError"></span>
        <div class="input-group date dtpStartDateTime" data-target-input="nearest">
            <input id="loanStartDate" type="text" class="form-control datetimepicker-input" data-target=".dtpStartDateTime"/>
            <div class="input-group-append" data-target=".dtpStartDateTime" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>

        <!-- Loan End Date -->
        <label id="loanEndDateLabel">End Date</label>
        <span id="end_dateError" class="inputError"></span>
        <div class="input-group date dtpEndDateTime" data-target-input="nearest">
            <input id="loanEndDate" type="text" class="form-control datetimepicker-input" data-target=".dtpEndDateTime"/>
            <div class="input-group-append" data-target=".dtpEndDateTime" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>

        <!-- User Selected -->
        <label id="userSelectedLabel">User</label>
        <span id="user_idError" class="inputError"></span>
        <select class="form-control" id="userSelected">
            <option>Please select a user...</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
            @endforeach
        </select>

        <!-- Equipment -->
        <label id="equipmentTableLabel">Equipment</label>
        <span id="detailsError" class="inputError"></span>
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
        <span id="status_idError" class="inputError"></span>
        <label class="form-check-label" for="defaultCheck1">
            Reservation
        </label>
        </div>
    </form>
</div>