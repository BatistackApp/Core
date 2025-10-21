<?php

namespace App\Filament\Admin\Pages;

use App\Models\Core\Company;
use Filament\Pages\Page;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class Settings extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    protected string $view = 'filament.admin.pages.settings';
    protected static ?string $title = 'Paramètres Générales';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-8-tooth';
    public ?array $data = [];

    public function mount()
    {        
        $this->form->fill(Company::query()->first()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Société')
                    ->description("Paramètres généraux de la société")
                    ->schema([
                        Section::make('Informations')
                            ->aside()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom de la société')
                                    ->required(),

                                TextInput::make('address')
                                    ->label('Adresse de la société')
                                    ->required(),  
                                    
                                Grid::make()
                                    ->columns(12)
                                    ->schema([
                                        TextInput::make('code_postal')
                                            ->label('Code Postal')
                                            ->required()
                                            ->columnSpan(3),

                                        TextInput::make('ville')
                                            ->label('Ville')
                                            ->required()
                                            ->columnSpan(5),

                                        TextInput::make('pays')
                                            ->label('Pays')
                                            ->required()
                                            ->columnSpan(4),
                                    ]), 
                            ]),

                        Section::make('Fiscalité')
                            ->description("Paramètres fiscaux de la société")
                            ->aside()
                            ->schema([
                                TextInput::make('num_tva')
                                    ->label('Numéro de TVA'),

                                Grid::make()
                                    ->columns(12)
                                    ->schema([
                                        TextInput::make('siret')
                                            ->label('Numéro de SIRET')
                                            ->columnSpan(10),

                                        TextInput::make('ape')
                                            ->label('Numéro d\'APE')
                                            ->columnSpan(2),
                                    ]), 
                            ]),  
                    ])                    
                    ->collapsible()
                    ->footer([
                        Action::make('submit')
                            ->label("Valider")
                            ->action(fn () => $this->updateSetting()),
                    ]),
            ])
            ->statePath('data');
    }

    public function updateSetting()
    {
        try {
            Company::query()
            ->first()
            ->update($this->form->getState());

            Notification::make()
                ->title("Paramètres mis à jour")    
                ->success()
                ->send();
        } catch(Exception $ex) {
            Notification::make()
                ->title("Erreur")
                ->body($ex->getMessage())
                ->danger()
                ->send();
        }
    }


}
