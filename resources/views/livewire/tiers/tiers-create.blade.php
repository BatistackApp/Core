<div>
    <form wire:submit="createTiers">
        {{ $this->form }}

        <div class="flex justify-end mt-5 gap-5">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" wire:click="verifySiren" >Vérifier le Siren</a>
        </div>
    </form>
</div>
