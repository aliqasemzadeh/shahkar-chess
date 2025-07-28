<?php

namespace App\Livewire\Administrator\GameManagement;

use App\Models\Chess\Game;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Create extends Component
{
    public $whitePlayerSearch = '';
    public $blackPlayerSearch = '';
    public $whitePlayerId = null;
    public $blackPlayerId = null;
    public $startAt = null;
    public $endAt = null;
    public $whitePlayerCode = '';
    public $blackPlayerCode = '';

    public function create()
    {
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

        Game::create([
            'white_player_user_id' => $this->whitePlayerId,
            'black_player_user_id' => $this->blackPlayerId,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'white_player_code' => $this->whitePlayerCode,
            'black_player_code' => $this->blackPlayerCode,
        ]);

        $this->resetForm();
        $this->dispatch('pg:eventRefresh-administrator.game-management.game.table');
        Flux::modal('administrator.game-management.create.modal')->close();
        
        // Show success message
        session()->flash('success', __('jetadmin.game_created_successfully'));
    }

    public function resetForm()
    {
        $this->whitePlayerSearch = '';
        $this->blackPlayerSearch = '';
        $this->whitePlayerId = null;
        $this->blackPlayerId = null;
        $this->startAt = null;
        $this->endAt = null;
        $this->whitePlayerCode = '';
        $this->blackPlayerCode = '';
        $this->resetValidation();
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
        return view('livewire.administrator.game-management.create');
    }
}
