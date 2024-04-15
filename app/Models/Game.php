<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'link',
        'screenshot',
        'numberOfPlays',
        'market',
    ];

    public function toCardData(): array
    {
        return [
            'uuid' => uniqid(),
            'name' => $this->name,
            'url' => $this->link,
            'image' => $this->screenshot,
            'ordering' => $this->numberOfPlays,
            'market' => $this->market,
            'type' => 'game'
        ];
    }
}
