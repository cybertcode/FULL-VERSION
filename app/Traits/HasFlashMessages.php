<?php

namespace App\Traits;

trait HasFlashMessages
{
    protected function flashSuccess(string $message): void
    {
        session()->flash('flash', ['type' => 'success', 'message' => $message]);
    }

    protected function flashError(string $message): void
    {
        session()->flash('flash', ['type' => 'error', 'message' => $message]);
    }

    protected function flashWarning(string $message): void
    {
        session()->flash('flash', ['type' => 'warning', 'message' => $message]);
    }

    protected function flashInfo(string $message): void
    {
        session()->flash('flash', ['type' => 'info', 'message' => $message]);
    }
}
