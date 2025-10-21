<x-filament-panels::page>
    <form wire:submit="updateSetting" class="flex-column gap-5">
        <div class="pb-5">
            {{ $this->form }}
        </div>
    </form>
</x-filament-panels::page>
