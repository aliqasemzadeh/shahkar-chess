<?php

namespace App\Livewire\Administrator\GameManagement;

use App\Models\Chess\Game;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    public Game $game;
    public $whitePlayerSearch = '';
    public $blackPlayerSearch = '';
    public $whitePlayerId = null;
    public $blackPlayerId = null;
    public $startAt = null;
    public $endAt = null;
    public $whitePlayerCode = '';
    public $blackPlayerCode = '';

    #[On('administrator.game-management.game.edit.assign-data')]
    public function assignData($id): void
    {
        $this->game = Game::findOrFail($id);
        
        // Set form data
        $this->whitePlayerId = $this->game->white_player_user_id;
        $this->blackPlayerId = $this->game->black_player_user_id;
        $this->startAt = $this->game->start_at;
        $this->endAt = $this->game->end_at;
        $this->whitePlayerCode = $this->game->white_player_code;
        $this->blackPlayerCode = $this->game->black_player_code;

        Flux::modal('administrator.game-management.edit.modal')->show();
    }

    public function edit()
    {
        if (!isset($this->game)) {
            return;
        }

        $this->validate([
            'whitePlayerId' => 'required|exists:users,id',
            'blackPlayerId' => 'required|exists:users,id|different:whitePlayerId',
            'startAt' => 'nullable|date',
            'endAt' => 'nullable|date|after:startAt',
            'whitePlayerCode' => 'nullable|string|max:255',
            'blackPlayerCode' => 'nullable|string|max:255',
        ], [
            'whitePlayerId.required' => __('validation.required', ['attribute' => __('jetadmin.white_player')]),
            'whitePlayerId.exists' => __('validation.exists', ['attribute' => __('jetadmin.white_player')]),
            'blackPlayerId.required' => __('validation.required', ['attribute' => __('jetadmin.black_player')]),
            'blackPlayerId.exists' => __('validation.exists', ['attribute' => __('jetadmin.black_player')]),
            'blackPlayerId.different' => __('validation.different', ['attribute' => __('jetadmin.black_player'), 'other' => __('jetadmin.white_player')]),
            'startAt.date' => __('validation.date', ['attribute' => __('jetadmin.start_at')]),
            'endAt.date' => __('validation.date', ['attribute' => __('jetadmin.end_at')]),
            'endAt.after' => __('validation.after', ['attribute' => __('jetadmin.end_at'), 'date' => __('jetadmin.start_at')]),
        ]);

        $this->game->update([
            'white_player_user_id' => $this->whitePlayerId,
            'black_player_user_id' => $this->blackPlayerId,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'white_player_code' => $this->whitePlayerCode,
            'black_player_code' => $this->blackPlayerCode,
        ]);

        $this->dispatch('pg:eventRefresh-administrator.game-management.game.table');
        Flux::modal('administrator.game-management.edit.modal')->close();
        
        // Show success message
        session()->flash('success', __('jetadmin.game_updated_successfully'));
    }

    #[\Livewire\Attributes\Computed]
    public function whitePlayers() {
        return User::query()
            ->when($this->whitePlayerSearch, fn($query) => $query->where('name', 'like', '%' . $this->whitePlayerSearch . '%'))
            ->limit(20)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function blackPlayers() {
        return User::query()
            ->when($this->blackPlayerSearch, fn($query) => $query->where('name', 'like', '%' . $this->blackPlayerSearch . '%'))
            ->limit(20)
            ->get();
    }

    public function render()
    {
        return view('livewire.administrator.game-management.edit');
    }
}
