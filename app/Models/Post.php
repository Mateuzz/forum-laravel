<?php

namespace App\Models;

use App\Casts\HtmlCleanInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Lib\FullText;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

enum PostOrderTypes: string {
    case Latest = 'latest';
    case Oldest = 'oldest';
    case RecentRelevant = 'recent-relevant';
    case MostViewed = 'most-viewed';
};

/**
 *
 *
 * @mixin IdeHelperPost
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property mixed $body
 * @property string $excerpt
 * @property int $views_count
 * @property int $unique_views_count
 * @property int $comments_count
 * @property int $stars
 * @property int|null $user_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $search
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Category $category
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static Builder|Post filtered(array $filters)
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post ordered(?string $orderMethod)
 * @method static Builder|Post query()
 * @method static Builder|Post whereBody($value)
 * @method static Builder|Post whereCategoryId($value)
 * @method static Builder|Post whereCommentsCount($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereExcerpt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereSearch($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereStars($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUniqueViewsCount($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereUserId($value)
 * @method static Builder|Post whereViewsCount($value)
 * @method static Builder|Post filter(array $filters)
 * @method static Builder|Post order(?string $orderMethod)
 * @mixin \Eloquent
 */
class Post extends Model {
    use HasFactory;

    protected $hidden = [
        'search',
    ];

    protected $casts = [
        'body' => HtmlCleanInput::class,
    ];

    const MAX_TITLE_SIZE = 100;
    const MAX_SLUG_SIZE = 100;
    const MAX_BODY_SIZE = 65535;
    const MAX_EXCERPT_SIZE = 255;

    const ALLOWED_ORDER_TYPES = ['latest', 'oldest', 'recent-relevant', 'most-viewed'];

    public static function getVisibleFieldsForIndex() {
        return ['id', 'title', 'slug', 'views_count', 'comments_count', 'stars', 'user_id', 'created_at', 'category_id', 'updated_at', 'excerpt'];
    }

    /* public static function getVisibleFieldsForShow() { */
    /*     return ['id', 'title', 'slug', 'body', 'views_count', 'comments_count', 'stars', 'user_id', 'category_id', 'created_at']; */
    /* } */

    /**
     * @param Builder $query
     * */
    public function scopeFilter($query, array $filters) {
        if (isset($filters['category'])) {
            $query->whereHas('category',
                    fn($query) =>
                        $query->where('slug', '=', $filters['category'])
            );
        }

        if (isset($filters['user'])) {
            $query->whereHas('author',
                    fn($query) =>
                        $query->where('id', '=', $filters['user'])
            );
        }

        /* if (isset($filters['search'])) { */
        /*     $query->whereRaw('MATCH(title, body) AGAINST(? IN BOOLEAN MODE)', $filters['search']); */
        /* } */

        if (isset($filters['search'])) {
            $driver = DB::getDriverName();

            switch ($driver) {
            case 'pgsql':
                $s = FullText::transformSearch($filters['search']);

                    // order by title (0.1 weight) and body (1.0 weight)
                $query->whereRaw("search @@ to_tsquery(?)", [$s])
                      ->orderByRaw( "ts_rank(ARRAY[0, 0, 0.1, 1]::float4[], search, to_tsquery(?)) desc", [$s]);

                break;

            default:
                /* Log::error("Database driver ($driver) not supported"); */
                /* abort(500, 'Internal Server Error'); */
                throw new \LogicException("Database driver ($driver) not supported");
            }
        }
    }

    /**
     * @param Builder $query
     * */
    public function scopeOrder($query, ?string $orderMethod) {
        switch (PostOrderTypes::from($orderMethod ?? 'latest')) {
        case PostOrderTypes::Latest:
            $query->orderByDesc('created_at');
            break;

        case PostOrderTypes::Oldest:
            $query->orderBy('created_at');
            break;

        case PostOrderTypes::RecentRelevant: {
            $query->where('created_at', '>=', now()->subWeek())
                ->orderBy('views_count', 'desc');
        } break;

        case PostOrderTypes::MostViewed:
            $query->orderBy('views_count', 'desc');
            break;
        }
    }

    public static function getRelationsConstraints() {
        return [
            'author' => fn ($builder) => $builder->select(['id', 'username', 'image']),
            'category' => fn ($builder) => $builder->select(['id', 'title', 'slug']),
        ];
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
