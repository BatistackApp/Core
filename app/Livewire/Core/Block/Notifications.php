<?php

namespace App\Livewire\Core\Block;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public $notifications;

    public string $icon;

    public function mount(): void
    {
        $this->notifications = Auth::user()->unreadNotifications;
    }

    public function readNotification(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.core.block.notifications');
    }

    public function getIconNotification($id): string
    {
        $notif = $this->notifications->where('id', $id)->first();

        return $notif['data']['icon'];
    }
}
