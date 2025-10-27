<?php

namespace App\Livewire\Tiers;

use App\Enums\Tiers\TiersNature;
use App\Enums\Tiers\TiersType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\Siren;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

#[Title('Liste des Tiers')]
#[Layout('components.layouts.tiers')]
class TiersCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas, InteractsWithActions;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->components([
                        Section::make('Générale')
                            ->components([
                                TextInput::make('identite')
                                    ->required()
                                    ->label('Identité'),

                                Select::make('nature')
                                    ->required()
                                    ->label('Nature')
                                    ->options(TiersNature::array()),

                                Select::make('type')
                                    ->required()
                                    ->label('Type')
                                    ->options(TiersType::plucks()),

                                

                                Grid::make(2)
                                    ->components([
                                        TextInput::make('siren')
                                            ->label("Siren"),

                                        Action::make('verifySiren')
                                            ->label('Vérifier le Siren')
                                            ->action('verifySiren'),
                                    ]),    
                                    
                                Toggle::make('tva')
                                    ->live()
                                    ->label('Possède un numéro de TVA'),
                                    
                                TextInput::make('num_tva')
                                    ->label('Numéro de TVA')
                                    ->visible(fn (Get $get) => $get('tva')),    
                            ])
                    ])
            ])
            ->statePath('data');
    }

    public function createTiers()
    {
        dd($this->form->getState());
    }

    public function verifySiren()
    {
        dd(app(Siren::class)->call($this->form->getState()['siren'], 'bool'));
    }

    public function render()
    {
        return view('livewire.tiers.tiers-create');
    }
}
