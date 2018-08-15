<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repositories extends Model
{
    const TABLE = 'repositories';
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
        'repo_id',
        'node_id',
        'owner',
        'owner_type',
        'name',
        'full_name',
        'html_url',
        'description',
        'homepage',
        'has_issues',
        'has_projects',
        'has_downloads',
        'has_wiki',
        'size',
        'stargazers_count',
        'watchers_count',
        'network_count',
        'subscribers_count',
        'forks',
        'open_issues',
        'default_branch',
        'created',
        'updated'
    ];

    /**
     * @param array $data
     */
    public function store(array $data)
    {
        $current = $this
            ->where('full_name', $data['full_name'])
            ->get();

        if (!$current->first()) {
            $this->create($data);
        } else {
            $this
                ->where('full_name', $data['full_name'])
                ->update($data);
        }
    }
}
