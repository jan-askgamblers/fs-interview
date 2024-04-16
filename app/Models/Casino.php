<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casino extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'logo',
        'rank',
        'market',
    ];

    public function toCardData(): array
    {
        return [
            'uuid' => uniqid(),
            'name' => $this->name,
            'url' => $this->url,
            'image' => $this->logo,
            'ordering' => $this->rank,
            'market' => $this->market,
            'type' => 'casino'
        ];
    }
}
