<div>
    <form wire:submit="updateSetting">
        {{ $this->form }}

        <div class="flex justify-end mt-5">
            <button class="btn btn-primary" type="submit">
                Enregistrer
            </button>
        </div>
    </form>
</div>
