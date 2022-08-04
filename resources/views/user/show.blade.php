@extends('layouts.app')

@section('mainContent')
    <h1>User #{{ $user->id }}</h1>

    <h2>{{ $user->forename }}</h2>

    <h3>{{ $user->surname }}</h3>

    <p>{{ $user->email }}</p>
@endsection