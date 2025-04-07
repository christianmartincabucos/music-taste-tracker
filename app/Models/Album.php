<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'song_name',
        'artist_name',
        'cover_image',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['total_votes'];

    /**
     * Get the users that have voted on the album.
     */
    public function voters()
    {
        return $this->belongsToMany(User::class, 'album_votes')
            ->withPivot('vote')
            ->withTimestamps();
    }

    /**
     * Get the total votes for the album.
     */
    public function getTotalVotesAttribute()
    {
        // If we have calculated_votes available (from the scope), use it
        if (isset($this->calculated_votes)) {
            return (int) $this->calculated_votes;
        }
        
        // Otherwise calculate it manually
        return $this->voters()->sum('vote');
    }

    /**
     * Scope a query to order albums by votes and then alphabetically.
     */
    public function scopeOrderByVotesAndName($query)
    {
        return $query
            ->leftJoin('album_votes as upvotes', function($join) {
                $join->on('albums.id', '=', 'upvotes.album_id')
                    ->where('upvotes.vote', '=', 1);
            })
            ->leftJoin('album_votes as downvotes', function($join) {
                $join->on('albums.id', '=', 'downvotes.album_id')
                    ->where('downvotes.vote', '=', -1);
            })
            ->select('albums.*')
            ->selectRaw('
                (COUNT(DISTINCT upvotes.id) - COUNT(DISTINCT downvotes.id)) as calculated_votes
            ')
            ->groupBy('albums.id')
            ->orderByRaw('calculated_votes DESC, song_name ASC');
    }
}

