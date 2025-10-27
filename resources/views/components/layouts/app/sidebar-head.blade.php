<flux:header sticky class="flex justify-between bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item href="{{ route('dashboard') }}">Acceuil</flux:navbar.item>
        @foreach (App\Models\Core\Module::all() as $module)
            <flux:navbar.item href="{{ file_exists(base_path('routes/'.$module->slug.'.php')) ? route($module->slug.'.dashboard') : '#' }}">{{ $module->name }}</flux:navbar.item>
        @endforeach
    </flux:navbar>
    <flux:navbar scrollable class="gap-3">
        @livewire('core.block.notifications')
        <a href="//saas.{{ config('batistack.domain') }}/changelog" class="badge badge-secondary badge-sm">Version: {{ get_version() }}</a>
    </flux:navbar>
</flux:header>
