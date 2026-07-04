@extends('admin/layouts/master')

@section('title', 'Crear equipo')

@section('admin-content')

<x-breadcrumb title="Crear equipo" :items="[['label' => 'Mi Equipo', 'url' => route('teams.show', auth()->user()->currentTeam)], ['label' => 'Crear equipo']]" />

@livewire('teams.create-team-form')

@endsection