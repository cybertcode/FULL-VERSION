<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
