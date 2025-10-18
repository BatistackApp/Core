<x-filament-panels::page>
    <form wire:submit="updateSetting" class="flex-column gap-5">
        <div class="pb-5">
            {{ $this->form }}
        </div>

        <x-filament::button type="submit" color="primary" size="lg" icon="heroicon-o-check-circle" icon-position="before">
            Valider
        </x-filament::button>
    </form>
</x-filament-panels::page>
