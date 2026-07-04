@component('mail::message')
  ¡Has sido invitado a unirte al equipo **{{ $invitation->team->name }}**!

  @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()) && setting('registration_enabled', true))
    Si no tienes una cuenta, puedes crear una haciendo clic en el botón de abajo. Después de crear tu cuenta, podrás hacer clic en el botón de aceptar invitación de este correo para unirte al equipo:

    @component('mail::button', ['url' => route('register')])
      Crear cuenta
    @endcomponent

    Si ya tienes una cuenta, puedes aceptar esta invitación haciendo clic en el botón de abajo:
  @else
    Puedes aceptar esta invitación haciendo clic en el botón de abajo:
  @endif


  @component('mail::button', ['url' => $acceptUrl])
    Aceptar invitación
  @endcomponent

  Si no esperabas recibir una invitación a este equipo, puedes ignorar este correo.
@endcomponent
