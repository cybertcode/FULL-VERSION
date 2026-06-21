<?php

namespace App\Traits;

trait HasFlashMessages
{
    protected function flashSuccess(string $message, ?string $title = null): void
    {
        session()->flash('flash', ['type' => 'success', 'message' => $message, 'title' => $title]);
    }

    protected function flashError(string $message, ?string $title = null): void
    {
        session()->flash('flash', ['type' => 'error', 'message' => $message, 'title' => $title]);
    }

    protected function flashWarning(string $message, ?string $title = null): void
    {
        session()->flash('flash', ['type' => 'warning', 'message' => $message, 'title' => $title]);
    }

    protected function flashInfo(string $message, ?string $title = null): void
    {
        session()->flash('flash', ['type' => 'info', 'message' => $message, 'title' => $title]);
    }
}
