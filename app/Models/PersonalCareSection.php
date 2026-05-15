<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalCareSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'description',
        'background_image',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PersonalCareFeature::class)->orderBy('order');
    }
}