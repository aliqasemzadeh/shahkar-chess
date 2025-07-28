<?php

namespace App\Livewire\User\Game;

use Livewire\Component;

class Index extends Component
{
    use \Livewire\WithPagination;

    public $sortBy = 'start_at';
    public $sortDirection = 'desc';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function games()
    {
        $user = auth()->user();
        
        return \App\Models\Chess\Game::query()
            ->where(function ($query) use ($user) {
                $query->where('white_player_user_id', $user->id)
                      ->orWhere('black_player_user_id', $user->id);
            })
            ->with(['whitePlayer', 'blackPlayer'])
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.user.game.index');
    }
}
