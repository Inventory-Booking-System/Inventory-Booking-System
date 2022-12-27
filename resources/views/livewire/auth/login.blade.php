<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
        <div class="col-6">
            <div class="card">
                <div class="card-header text-center"><strong>Inventory Booking System Login</strong></div>
                <div class="card-body">
                    <form wire:submit.prevent="login" action="#" method="POST">
                        <div class="form-group">
                            <label for="InputEmail1">Email address</label>
                            <input wire:model="email" type="email" class="form-control" id="InputEmail1" placeholder="Email address"></div>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="form-group">
                            <label for="InputPassword1">Password</label>
                            <input wire:model.lazy="password" type="password" class="form-control" id="InputPassword1" placeholder="Password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>