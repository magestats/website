<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PullRequests extends Model
{
    const TABLE = 'pull_requests';
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
        'pr_id',
        'node_id',
        'html_url',
        'number',
        'repo',
        'state',
        'title',
        'author',
        'author_association',
        'labels',
        'label_ids',
        'milestone',
        'milestone_url',
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
