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
 * @property string $counterparty_fee 第三方费用
 * @property string $media_fee 媒体费
 * @property int $team_id
 * @property int $product_id
 * @property string $transaction_fee 手续费
 * @property string $service_fee 服务费
 * @property string $usd_loss_percent U损
 * @property string $usd
 * @property string $ustd
 * @property string $commission 返点
 * @property string $purpose 用途
 * @property string $description 费用明细
 * @property string $account 账户信息
 * @property string $handler 经手人
 * @property string $remark 备注
 * @property string $balance 余额
 * @property int $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance filter(\Caleb\Practice\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCounterpartyFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereHandler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereMediaFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereTransactionFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUsdLossPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUstd($value)
 * @property int $agent_id
 * @property-read \App\Models\Agent|null $agent
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereAgentId($value)
 * @mixin \Eloquent
 */
class Finance extends Model
{
    use Standardization;

    protected $fillable = [
        'month',
        'date',
        'department_id',
        'counterparty_fee',
        'media_fee',
        'team_id',
        'product_id',
        'transaction_fee',
        'service_fee',
        'usd_loss_percent',
        'usd',
        'ustd',
        'commission',
        'purpose',
        'description',
        'account',
        'handler',
        'remark',
        'balance',
        'creator_id',
        'agent_id',
    ];

    protected $casts = [
        'usd_loss_percent' => 'float',
        'usd' => 'float',
        'ustd' => 'float',
        'commission' => 'float',
        'balance' => 'float',
        'counterparty_fee' => 'float',
        'media_fee' => 'float',
        'transaction_fee' => 'float',
        'service_fee' => 'float',
    ];

    public static function booted()
    {
        static::creating(function (Finance $usage) {
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
