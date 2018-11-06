<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contributors extends Model
{
    const TABLE = 'contributors';
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
        'author',
        'first_contribution',
        'name',
        'company',
        'blog',
        'location',
        'bio',
        'author_id',
        'node_id',
        'meta'
    ];

    public function store(array $data)
    {
        $current = $this
            ->where('author', $data['author'])
            ->get();

        if (!$current->first()) {
            $this->create($data);
        } else {
            unset($data['first_contribution']);
            $this
                ->where('author', $data['author'])
                ->update($data);
        }
    }
}
