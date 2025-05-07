<?php

namespace App\Models;

use Caleb\Practice\Standardization;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string|null $meta
 * @property string $permission
 * @property int $sort
 * @property int $pid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filter(\Caleb\Practice\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Menu> $children
 * @property-read int|null $children_count
 * @property-read Menu|null $parent
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use Standardization;

    protected $fillable = [
        'title',
        'type',
        'meta',
        'permission',
        'sort',
        'pid',
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'pid', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'pid', 'id');
    }
}
