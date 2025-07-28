<?php

namespace App\Models\Chess;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpseclib3\Exception\BadConfigurationException;

class Game extends Model
{
    use SoftDeletes;

    public $fillable = [
        'white_player_user_id',
        'black_player_user_id',
        'start_at',
        'end_at',
        'white_player_code',
        'black_player_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function whitePlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'white_player_user_id');
    }

    public function blackPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'black_player_user_id');
    }

    /**
     * Check if the given user is the white player in this game
     */
    public function isWhitePlayer(User $user): bool
    {
        return $this->white_player_user_id === $user->id;
    }

    /**
     * Check if the given user is the black player in this game
     */
    public function isBlackPlayer(User $user): bool
    {
        return $this->black_player_user_id === $user->id;
    }

    /**
     * Get the role of the given user in this game (white, black, or null if not a player)
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->isWhitePlayer($user)) {
            return 'white';
        }
        
        if ($this->isBlackPlayer($user)) {
            return 'black';
        }
        
        return null;
    }
}
