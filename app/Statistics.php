<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    const TABLE = 'statistics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'node_id',
        'state',
        'repo',
        'title',
        'author',
        'author_association',
        'created',
        'updated',
        'closed',
        'merged',
        'meta',
    ];

    public function store(array $data)
    {
        $current = $this
            ->where('number', $data['number'])
            ->where('repo', $data['repo'])
            ->get();

        if (!$current->first()) {
            $this->create($data);
        } else {
            $this
                ->where('number', $data['number'])
                ->where('repo', $data['repo'])
                ->update($data);
        }
    }
}
