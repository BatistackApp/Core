<?php

namespace App\Livewire\Core\Page;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Notifications\Notification as NotificationsNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mes Notifications')]
#[Layout('components.layouts.core')]
class Notification extends Component implements HasTable, HasSchemas, HasActions
{
    use InteractsWithTable, InteractsWithSchemas, InteractsWithActions;

    public $notifications;

    public function mount()
    {
        $this->notifications = Auth::user()->notifications()->get();
    }

    public function render()
    {
        return view('livewire.core.page.notification');
    }
}
