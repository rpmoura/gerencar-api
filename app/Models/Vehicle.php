<?php

namespace App\Models;

use App\Traits\Uuid\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Vehicle extends Model
{
    use UseUuid;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand',
        'model',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_x_vehicles', 'vehicle_id', 'user_id');
    }
}
