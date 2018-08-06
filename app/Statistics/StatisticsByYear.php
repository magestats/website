<?php

namespace App\Statistics;

use Illuminate\Database\Eloquent\Model;

class StatisticsByYear extends Model
{
    const TABLE = 'statistics_by_year';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'author',
        'created',
        'open',
        'closed',
        'merged',
    ];

    public function store(array $data)
    {
        $current = $this
            ->where('year', $data['year'])
            ->where('author', $data['author'])
            ->get();

        if (!$current->first()) {
            $this->create($data);
        } else {
            $this
                ->where('year', $data['year'])
                ->where('author', $data['author'])
                ->update($data);
        }
    }
}
