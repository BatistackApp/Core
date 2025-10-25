@if(request()->routeIs('dashboard', 'profile.edit', 'password.edit', 'appearance.edit', 'two-factor.show'))
<flux:sidebar.nav>
    <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" current>Tableau de Bord</flux:sidebar.item>
</flux:sidebar.nav>
@endif