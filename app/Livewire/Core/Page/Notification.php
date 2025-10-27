<?php

namespace App\Livewire\Core\Page;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
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
        $this->notifications = DatabaseNotification::query()->where('notifiable_id', Auth::user()->id)->get();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->notifications)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date'),

                TextColumn::make('data.title')
                    ->label('Notification')
                    ->description(fn (Model $record) => $record->data['body']),                    
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Model $record) => !$record->read_at)
                    ->action(fn (Model $record) => $record->markAsRead()),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->visible(fn (Model $record) => !$record->read_at)
                    ->action(fn (Model $record) => $record->delete()),
            ]);
    }

    public function render()
    {
        return view('livewire.core.page.notification');
    }
}
