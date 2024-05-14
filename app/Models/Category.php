<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @mixin IdeHelperCategory
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property-read int|null $posts_count
 * @property int $comments_count
 * @property-write int|null $parent_id
 * @property int $depth
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTitle($value)
 * @mixin \Eloquent
 */
class Category extends Model {
    use HasFactory;

    const MAX_TITLE_SIZE = 50;
    const MAX_SLUG_SIZE = 80;

    public $timestamps = false;

    protected function parentId() : Attribute {
        return Attribute::make(
            set: fn (?int $value) => [
                'parent_id' => $value,
                'depth' => $value ? Category::findOrFail($value)->depth + 1 : 0
            ]);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }
}
