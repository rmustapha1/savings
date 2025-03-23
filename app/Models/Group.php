<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name',
        'monthly_contribution',
        'total_members',
        'status',
    ];

    /**
     * Get the members associated with the group.
     */
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    /**
     * Get active groups.
     */
    protected static function booted() {
        static::addGlobalScope('status', function (Builder $builder) {
            return $builder->where('status', 1);
        });
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\User', 'created_user_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->belongsTo('App\Models\User', 'updated_user_id')->withDefault();
    }
}
