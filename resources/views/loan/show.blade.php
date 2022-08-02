@extends('layouts.app')

@section('mainContent')
    <h1>Loan #{{ $loan->id }}</h1>

    <h2>{{ $loan->user->forename }} {{ $loan->user->surname }}</h2>

    <h3>{{ $loan->start_date_time }} - {{ $loan->end_date_time }}</h3>

    <p>{{ $loan->details }}</p>

    <p>{{ $loan->status_id }}</p>
@endsection

@push('scripts')
@endpush