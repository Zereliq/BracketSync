@extends('layouts.app')

@section('content')
    @include('partials.hero')
    @include('partials.tournaments-grid')
    @include('partials.matches-list')
    @include('partials.statistics')
    @include('partials.how-it-works')
    @include('partials.cta')
@endsection
