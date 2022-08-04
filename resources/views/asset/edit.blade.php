@extends('layouts.app')

@section('mainContent')
    <form action="/assets/{{ $asset->id }}" method="POST" enctype="multipart/form-data" >
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Start Date/Time, User, Equipment Selection, Additional Details, Reservation -->
            <div class="col-lg-5 offset-lg-3 p-3">
                <!-- Asset Name -->
                <label id="assetNameLabel">Name</label>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <input type="text" name="name" class="form-control" id="assetName" value="{{ old('name', $asset->name) }}">

                <!-- Asset Description -->
                <label id="assetDescriptionLabel">Description</label>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <textarea rows="8" name="description" class="form-control" id="assetDescription">{{ old('description', $asset->description) }}</textarea>

                <!-- Asset Tag -->
                <label class="mt-3" id="assetTagLabel">Tag</label>
                @if($errors->has('tag'))
                    <span class="text-danger">{{ $errors->first('tag') }}</span>
                @endif
                <input type="text" name="tag" class="form-control" id="assetTag" value="{{ old('tag', $asset->tag) }}">

                <button type="submit" class="btn btn-primary btn-block mt-3">Update Asset</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    {{-- <script src="{{ asset('js/loans.js') }}"></script> --}}
@endpush