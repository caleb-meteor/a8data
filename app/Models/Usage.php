<?php

namespace App\Models;

use Caleb\Practice\Standardization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int|null $month
 * @property Carbon|null $date
 * @property int $department_id
 * @property int $team_id
 * @property int $product_id
 * @property string $exclusive_agent 总代理
 * @property string $channel
 * @property string $media
 * @property int $agent_id
 * @property string $placement_method 投放方式
 * @property int $actual_usage 实际消耗
 * @property int $view 展示
 * @property int $click 点击
 * @property int $install 安装
 * @property int $send_num 发送条数
 * @property float $price 单价
 * @property string $unique_id
 * @property int $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage filter(\Caleb\Practice\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereActualUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereExclusiveAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereInstall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage wherePlacementMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereSendNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage whereView($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usage withoutTrashed()
 * @property-read \App\Models\Agent|null $agent
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Team|null $team
 * @mixin \Eloquent
 */
class Usage extends Model
{
    use Standardization;

    protected $fillable = [
        'month',
        'date',
        'department_id',
        'team_id',
        'product_id',
        'exclusive_agent',
        'channel',
        'media',
        'agent_id',
        'placement_method',
        'actual_usage',
        'view',
        'click',
        'install',
        'send_num',
        'price',
        'unique_id',
        'creator_id',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'float'
    ];

    public static function booted()
    {
        static::creating(function (Usage $usage) {
            $usage->creator_id = auth()->id();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }
}
