@php
use Illuminate\Support\Facades\Gate;
@endphp
<x-action-section>
  <x-slot name="title">
    Eliminar equipo
  </x-slot>

  <x-slot name="description">
    Elimina este equipo de forma permanente.
  </x-slot>

  <x-slot name="content">
    <p class="text-body-secondary">
      Una vez eliminado un equipo, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar este equipo, descarga cualquier dato o información que desees conservar.
    </p>

    <x-danger-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
      Eliminar equipo
    </x-danger-button>

    <!-- Delete Team Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingTeamDeletion">
      <x-slot name="title">
        Eliminar equipo
      </x-slot>

      <x-slot name="content">
        ¿Estás seguro de que deseas eliminar este equipo? Una vez eliminado, todos sus recursos y datos se eliminarán de forma permanente.
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
          Cancelar
        </x-secondary-button>

        <x-danger-button wire:click="deleteTeam" wire:loading.attr="disabled">
          Eliminar equipo
        </x-danger-button>
      </x-slot>
    </x-confirmation-modal>
  </x-slot>
</x-action-section>
