<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sessions';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'string',
        'last_activity' => 'datetime',
    ];

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('last_activity');
    }

}
