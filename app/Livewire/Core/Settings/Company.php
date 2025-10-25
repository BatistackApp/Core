<?php

namespace App\Livewire\Core\Settings;

use App\Models\Core\Company as CoreCompany;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Paramètre de la société")]
#[Layout("components.layouts.core")]
class Company extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas, InteractsWithActions;

    public CoreCompany $company;
    public ?array $data = [];

    public function mount(): void
    {
        $this->company = CoreCompany::first();
        $this->form->fill($this->company->toArray());        
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Mon Entreprise")
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom de l\'entreprise'),

                        TextInput::make('address')
                            ->label('Adresse de l\'entreprise'),

                        Grid::make(2)    
                            ->schema([
                                TextInput::make('code_postal')
                                    ->label('Code postal de l\'entreprise'),

                                TextInput::make('ville')
                                    ->label('Ville de l\'entreprise'),

                                TextInput::make('pays')
                                    ->label('Pays de l\'entreprise'),                                    
                            ])
                    ]),
                Section::make('Contact de l\'entreprise')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                ->label('Email de l\'entreprise'),

                                TextInput::make('phone')
                                ->label('Téléphone de l\'entreprise'),

                                TextInput::make('fax')
                                ->label('Fax de l\'entreprise'),

                                TextInput::make('web')
                                ->label('Site web de l\'entreprise')
                                ->url(),
                            ])

                    ]),

                Section::make('Information Fiscal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('siret')
                                ->label('Numéro SIRET de l\'entreprise'),

                                TextInput::make('ape')
                                ->label('Code APE de l\'entreprise'),

                                TextInput::make('num_tva')
                                ->label('Numéro TVA de l\'entreprise'),

                                TextInput::make('capital')
                                ->label('Capital de l\'entreprise'),
                            ]),
                        
                        TextInput::make('rcs')
                                ->label('Numéro RCS de l\'entreprise'),
                        ]),                            
            ])
            ->statePath('data')
            ->model($this->company);
    }

    public function updateSetting()
    {
        try {
            CoreCompany::first()->update($this->form->getState());
            Notification::make()
                ->title('Paramètres mis à jour')
                ->success()
                ->send();
        } catch(Exception $ex) {
            Log::emergency($ex->getMessage());
            Notification::make()
                ->title('Erreur')
                ->body($ex->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.core.settings.company');
    }
}
