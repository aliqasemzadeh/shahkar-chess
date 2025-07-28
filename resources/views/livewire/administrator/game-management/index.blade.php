<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('jetadmin.games') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('jetadmin.games_description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <livewire:administrator.game-management.create />
    <livewire:administrator.game-management.edit />
    <livewire:administrator.game-management.table />
</div>
