@extends('layouts.app')

@section('mainContent')
    <h1>Asset #{{ $asset->id }}</h1>

    <h2>{{ $asset->name }}</h2>

    <h3>{{ $asset->tag }}</h3>

    <p>{{ $asset->description }}</p>
@endsection