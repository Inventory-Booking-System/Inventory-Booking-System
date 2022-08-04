@extends('layouts.app')

@section('mainContent')
    <form action="/users" method="POST" enctype="multipart/form-data" >
        @csrf

        <div class="row">
            <div class="col-lg-5 offset-lg-3 p-3">
                <!-- Forename -->
                <label id="forenameLabel">Forename</label>
                @if($errors->has('forename'))
                    <span class="text-danger">{{ $errors->first('forename') }}</span>
                @endif
                <input type="text" name="forename" class="form-control" id="forename" value="{{ old('forename') }}">

                <!-- Surname -->
                <label class="mt-3" id="surnameLabel">Surname</label>
                @if($errors->has('surname'))
                    <span class="text-danger">{{ $errors->first('surname') }}</span>
                @endif
                <input type="text" name="surname" class="form-control" id="surname" value="{{ old('surname') }}">

                <!-- Email -->
                <label class="mt-3">Email</label>
                @if($errors->has('email'))
                    <p class="text-danger">{{ $errors->first('email') }}</p>
                @endif
                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}">

                <button type="submit" class="btn btn-primary btn-block mt-3">Create User</button>
            </div>
        </div>
    </form>
@endsection