<?php

namespace App\Livewire\User\Game;

use App\Models\Chess\Game;
use Livewire\Attributes\On;
use Livewire\Component;

class Play extends Component
{
    public Game $game;
    public $gameId;

    public $whitePlayer;
    public $blackPlayer;

    #[On('handleMoveData')]
    public function handleMoveData()
    {
        $this->js("alert('Fee')");
    }

    public function mount($id)
    {
        $this->gameId = $id;
        $this->game = Game::with(['whitePlayer', 'blackPlayer'])->findOrFail($id);

        // Check if the authenticated user is a player in this game
        $user = auth()->user();
        if (!$this->game->getUserRole($user)) {
            abort(403, __('jetadmin.not_player_in_game'));
        }
    }

    public function render()
    {
        return view('livewire.user.game.play');
    }
}
