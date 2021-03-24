<?php

namespace Uccello\Comment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uccello\Core\Support\Traits\UccelloModule;
use Uccello\Core\Support\Traits\RelatedlistTrait;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Comment extends Model implements Searchable
{
    use SoftDeletes;
    use UccelloModule;
    // use RelatedlistTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = $this->tablePrefix . '_comments';     ∕∕ TODO: Prefix...
    protected $table = 'uccello_comments';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public $searchableType = 'comment';

    public $searchableColumns = [
        'content',
    ];

    public function getSearchResult(): SearchResult
    {
        $text = $this->recordLabel;

        return new SearchResult(
            $this,
            $text
        );
    }

    protected function initTablePrefix()
    {
        $this->tablePrefix = env('UCCELLO_TABLE_PREFIX', 'uccello_');
    }

    /**
     * Returns record label
     *
     * @return string
     */
    public function getRecordLabelAttribute(): string
    {
        return ($this->entity ? $this->entity->recordLabel : '?') . ' (' . $this->user->recordLabel . ')';
    }


    /**
     * Get the entity that owns the comment.
     */
    public function getEntityAttribute()
    {
        // return $this->belongsTo('Uccello\Core\Models\Entity');
        return uccello()->getRecordByUuid($this->entity_id);
    }

    /**
     * Get the parent comment that owns the comment.
     */
    public function parent()
    {
        return $this->belongsTo('Uccello\Comment\Models\Comment')->withTrashed();
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    protected $repliesCache = null;

    /**
     * Get the replies (childen comments).
     */
    public function getRepliesAttribute()
    {
        if (!$this->repliesCache) {
            $order = config('uccello.comment.order_desc', true) ? 'desc' : 'asc';

            $query = static::where('parent_id', $this->id)
                            ->orderby('created_at', $order);

            if (config('uccello.comment.can_delete_parent', false)) {
                $query = $query->withTrashed();
            }

            $this->repliesCache = $query->get();
        }

        return $this->repliesCache;
    }

    public static function getAll($entity)
    {
        $order = config('uccello.comment.order_desc', true) ? 'desc' : 'asc';

        $query = static::where('entity_id', $entity->uuid)
                        ->whereNull('parent_id')
                        ->orderby('created_at', $order);

        if (config('uccello.comment.can_delete_parent', false)) {
            $query = $query->withTrashed();
        }

        return $query->get();
    }
}
