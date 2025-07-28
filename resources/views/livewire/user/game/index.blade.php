<div>
    <x-slot name="title">
        {{ __('jetadmin.games') }}
    </x-slot>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('jetadmin.games') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('jetadmin.games_description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <flux:table :paginate="$this->games">
        <flux:table.columns>
            <flux:table.column>{{ __('jetadmin.players') }}</flux:table.column>
            <flux:table.column>{{ __('jetadmin.your_color') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'start_at'" :direction="$sortDirection" wire:click="sort('start_at')">{{ __('jetadmin.start_date') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'end_at'" :direction="$sortDirection" wire:click="sort('end_at')">{{ __('jetadmin.end_date') }}</flux:table.column>
            <flux:table.column>{{ __('jetadmin.status') }}</flux:table.column>
            <flux:table.column>{{ __('jetadmin.action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->games as $game)
                @php
                    $user = auth()->user();
                    $userRole = $game->getUserRole($user);
                    $isWhitePlayer = $game->isWhitePlayer($user);
                    $isBlackPlayer = $game->isBlackPlayer($user);
                    
                    // Determine game status
                    $status = __('jetadmin.not_started');
                    $statusColor = 'green';
                    
                    if ($game->start_at && !$game->end_at) {
                        $status = __('jetadmin.in_progress');
                        $statusColor = 'red';
                    } elseif ($game->end_at) {
                        $status = __('jetadmin.ended');
                        $statusColor = 'yellow';
                    }
                @endphp
                
                <flux:table.row :key="$game->id">
                    <flux:table.cell class="flex items-center gap-3">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <flux:avatar size="xs" src="{{ $game->whitePlayer->profile_photo_url ?? '' }}" />
                                <span class="font-medium">{{ $game->whitePlayer->name }}</span>
                                <flux:badge color="solid" size="xs">{{ __('jetadmin.white') }}</flux:badge>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:avatar size="xs" src="{{ $game->blackPlayer->profile_photo_url ?? '' }}" />
                                <span class="font-medium">{{ $game->blackPlayer->name }}</span>
                                <flux:badge color="solid" size="xs">{{ __('jetadmin.black') }}</flux:badge>
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($isWhitePlayer)
                            <flux:badge color="solid" size="sm">{{ __('jetadmin.white') }}</flux:badge>
                        @elseif ($isBlackPlayer)
                            <flux:badge color="solid" size="sm">{{ __('jetadmin.black') }}</flux:badge>
                        @else
                            <flux:badge color="solid" size="sm">Zinc</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        {{ $game->start_at ? $game->start_at->format('M d, Y H:i') : __('jetadmin.not_started') }}
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        {{ $game->end_at ? $game->end_at->format('M d, Y H:i') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$statusColor" inset="top bottom">{{ $status }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            icon="chess" 
                            inset="top bottom"
                            href="{{ route('user.game.play', $game->id) }}"
                        >
                            {{ __('jetadmin.play') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
