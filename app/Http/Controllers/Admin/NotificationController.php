<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Notification\BroadcastNotificationRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class NotificationController extends BaseAdminController
{
    /** Listado completo de notificaciones del usuario autenticado. */
    public function index(Request $request): View
    {
        $query = $request->user()->notifications();

        if ($request->input('filtro') === 'no-leidas') {
            $query->whereNull('read_at');
        }

        return view('admin.notifications.index', [
            'notifications' => $query->paginate(15)->withQueryString(),
            'unreadCount' => $request->user()->unreadNotifications()->count(),
            'roles' => $request->user()->can('notifications.send') ? Role::pluck('name') : collect(),
        ]);
    }

    /** Marca una notificación como leída y redirige a su URL si tiene. */
    public function markRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Todas las notificaciones fueron marcadas como leídas.',
        ]);
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Notificación eliminada.',
        ]);
    }

    /** Envía una notificación masiva a todos los usuarios o a los de un rol específico. */
    public function broadcast(BroadcastNotificationRequest $request): RedirectResponse
    {
        $this->authorize('notifications.send');

        $data = $request->validated();

        $users = $data['audience'] === 'role'
            ? User::role($data['role'])->get()
            : User::all();

        Notification::send($users, new SystemNotification(
            title: $data['title'],
            message: $data['message'],
            icon: 'tabler-speakerphone',
            color: 'info',
            sendEmail: ! empty($data['send_email']),
        ));

        activity('notificaciones')
            ->causedBy($request->user())
            ->withProperties(['audience' => $data['audience'], 'role' => $data['role'] ?? null, 'count' => $users->count()])
            ->log("Notificación masiva enviada a {$users->count()} usuario(s).");

        return back()->with('flash', [
            'type' => 'success',
            'message' => "Notificación enviada a {$users->count()} usuario(s).",
        ]);
    }
}
