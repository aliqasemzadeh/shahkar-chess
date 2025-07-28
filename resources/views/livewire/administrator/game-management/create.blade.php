<flux:modal name="administrator.game-management.create.modal" variant="flyout" position="left">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('jetadmin.create_game') }}</flux:heading>
            <flux:text class="mt-2">{{ __('jetadmin.create_game_description') }}</flux:text>
        </div>

        <!-- Modal body -->
        <form wire:submit="create">
            <div class="space-y-4">
                <!-- White Player Selection -->
                <div>
                    <flux:label for="white-player">{{ __('jetadmin.white_player') }}</flux:label>
                    <flux:select wire:model="whitePlayerId" variant="combobox" :filter="false">
                        <x-slot name="input">
                            <flux:select.input
                                wire:model.live="whitePlayerSearch"
                                placeholder="{{ __('jetadmin.select_white_player') }}" />
                        </x-slot>
                        @foreach ($this->whitePlayers as $user)
                            <flux:select.option value="{{ $user->id }}" wire:key="white-{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('whitePlayerId')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Black Player Selection -->
                <div>
                    <flux:label for="black-player">{{ __('jetadmin.black_player') }}</flux:label>
                    <flux:select wire:model="blackPlayerId" variant="combobox" :filter="false">
                        <x-slot name="input">
                            <flux:select.input
                                wire:model.live="blackPlayerSearch"
                                placeholder="{{ __('jetadmin.select_black_player') }}" />
                        </x-slot>
                        @foreach ($this->blackPlayers as $user)
                            <flux:select.option value="{{ $user->id }}" wire:key="black-{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('blackPlayerId')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Start Time -->
                <div>
                    <flux:label for="start-at">{{ __('jetadmin.start_at') }}</flux:label>
                    <flux:input
                        wire:model="startAt"
                        type="datetime-local"
                        id="start-at" />
                    @error('startAt')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- End Time -->
                <div>
                    <flux:label for="end-at">{{ __('jetadmin.end_at') }}</flux:label>
                    <flux:input
                        wire:model="endAt"
                        type="datetime-local"
                        id="end-at" />
                    @error('endAt')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- White Player Code -->
                <div>
                    <flux:label for="white-player-code">{{ __('jetadmin.white_player_code') }}</flux:label>
                    <flux:input
                        wire:model="whitePlayerCode"
                        type="text"
                        id="white-player-code"
                        placeholder="{{ __('jetadmin.white_player_code') }}" />
                    @error('whitePlayerCode')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Black Player Code -->
                <div>
                    <flux:label for="black-player-code">{{ __('jetadmin.black_player_code') }}</flux:label>
                    <flux:input
                        wire:model="blackPlayerCode"
                        type="text"
                        id="black-player-code"
                        placeholder="{{ __('jetadmin.black_player_code') }}" />
                    @error('blackPlayerCode')
                        <flux:text variant="error" class="mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="$dispatch('modal-close', { name: 'administrator.game-management.create.modal' })">
                    {{ __('jetadmin.cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('jetadmin.create') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
