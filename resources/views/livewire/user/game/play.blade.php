<div>
    <x-slot name="title">
        {{ __('jetadmin.game') }} #{{ $game->id }}
    </x-slot>
    
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('jetadmin.game') }} #{{ $game->id }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('jetadmin.game_play_description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Game Information -->
        <div class="space-y-4">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading size="lg">Game Information</flux:heading>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="font-medium">Status:</span>
                        @php
                            $status = 'Not Started';
                            $statusColor = 'green';
                            
                            if ($game->start_at && !$game->end_at) {
                                $status = 'In Progress';
                                $statusColor = 'red';
                            } elseif ($game->end_at) {
                                $status = 'Ended';
                                $statusColor = 'yellow';
                            }
                        @endphp
                        <flux:badge :color="$statusColor" size="sm">{{ $status }}</flux:badge>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="font-medium">Start Date:</span>
                        <span>{{ $game->start_at ? $game->start_at->format('M d, Y H:i') : 'Not started' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="font-medium">End Date:</span>
                        <span>{{ $game->end_at ? $game->end_at->format('M d, Y H:i') : '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="font-medium">Your Color:</span>
                        @php
                            $user = auth()->user();
                            $userRole = $game->getUserRole($user);
                        @endphp
                        <flux:badge color="solid" size="sm">{{ ucfirst($userRole) }}</flux:badge>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Players Information -->
        <div class="space-y-4">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading size="lg">Players</flux:heading>
                </div>
                <div class="space-y-4">
                    <!-- White Player -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" src="{{ $game->whitePlayer->profile_photo_url ?? '' }}" />
                            <div>
                                <div class="font-medium">{{ $game->whitePlayer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $game->whitePlayer->email }}</div>
                            </div>
                        </div>
                        <flux:badge color="solid" size="sm">White</flux:badge>
                    </div>

                    <!-- Black Player -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" src="{{ $game->blackPlayer->profile_photo_url ?? '' }}" />
                            <div>
                                <div class="font-medium">{{ $game->blackPlayer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $game->blackPlayer->email }}</div>
                            </div>
                        </div>
                        <flux:badge color="solid" size="sm">Black</flux:badge>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>

    <!-- Chess Board Placeholder -->
    <div class="mt-8">
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Chess Board</flux:heading>
            </div>
            <div class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chess" class="w-16 h-16 text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">Chess board implementation coming soon...</p>
                </div>
            </div>
        </flux:card>
    </div>
</div>
