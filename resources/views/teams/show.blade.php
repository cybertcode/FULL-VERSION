@extends('admin/layouts/master')

@php
use Illuminate\Support\Facades\Gate;
@endphp

@section('title', 'Mi Equipo')

@section('admin-content')

<x-breadcrumb title="Mi Equipo" :items="[['label' => 'Mi Equipo']]" />

<div class="mb-6">
  @livewire('teams.update-team-name-form', ['team' => $team])
</div>

@livewire('teams.team-member-manager', ['team' => $team])

@if (Gate::check('delete', $team) && !$team->personal_team)
<div class="mt-6">
  @livewire('teams.delete-team-form', ['team' => $team])
</div>
@endif

@endsection