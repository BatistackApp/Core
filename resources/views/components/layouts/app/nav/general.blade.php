@if(request()->routeIs('dashboard', 'profile.edit', 'password.edit', 'appearance.edit', 'two-factor.show', 'core.*'))
<flux:sidebar.nav>
    <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" current="{{ request()->routeIs('dashboard') }}">Tableau de Bord</flux:sidebar.item>
    <flux:sidebar.group expandable :expanded="request()->routeIs('core.settings.*')" icon="cog" heading="Configuration" class="grid">
        <flux:sidebar.item :href="route('core.settings.company')" current="{{ request()->routeIs('core.settings.company') }}">Mon Entreprise</flux:sidebar.item>
    </flux:sidebar.group>
</flux:sidebar.nav>
@endif