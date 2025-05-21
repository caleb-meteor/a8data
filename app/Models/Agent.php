<?php

namespace App\Models;

use Caleb\Practice\Standardization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $commission
 * @property string $remark
 * @property string $media
 * @property int $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent filter(\Caleb\Practice\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent withoutTrashed()
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Finance> $finances
 * @property-read int|null $finances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Usage> $usages
 * @property-read int|null $usages_count
 * @mixin \Eloquent
 */
class Agent extends Model
{
    use Standardization, SoftDeletes;

    protected $fillable = [
        'name',
        'commission',
        'remark',
        'media',
        'creator_id',
    ];

    protected $casts = [
        'commission' => 'float',
    ];

    public static function booted()
    {
        static::creating(function (Agent $agent) {
            $agent->creator_id = auth()->id();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function usages()
    {
        return $this->hasMany(Usage::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
