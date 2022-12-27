<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
        <div class="col-6">
            <div class="card">
                <div class="card-header text-center"><strong>Inventory Booking System Register</strong></div>
                <div class="card-body">
                    <form wire:submit.prevent="login" action="#" method="POST">
                        <div class="form-group">
                            <label for="InputPassword">Password</label>
                            <input wire:model.lazy="password" type="password" class="form-control" id="InputPassword" placeholder="Password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="InputPasswordConfirmation">Password Confirmation</label>
                            <input wire:model.lazy="passwordConfirmation" type="password" class="form-control" id="InputPasswordConfirmation" placeholder="Password">
                            @error('passwordConfirmation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>