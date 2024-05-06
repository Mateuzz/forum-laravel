<?php

namespace App\Models;

use App\Casts\HtmlCleanInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Lib\FullText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

enum PostOrderTypes: string {
    case Latest = 'latest';
    case Oldest = 'oldest';
    case RecentRelevant = 'recent-relevant';
    case MostViewed = 'most-viewed';
};

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

    public function scopeFiltered(Builder $query, array $filters) {
        if (isset($filters['category'])) {
            $query->whereHas('category',
                    fn(Builder $query) =>
                        $query->where('slug', '=', $filters['category'])
            );
        }

        if (isset($filters['user'])) {
            $query->whereHas('author',
                    fn(Builder $query) =>
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
                $query->whereRaw("search @@ to_tsquery('$s')");
                break;

            default:
                /* Log::error("Database driver ($driver) not supported"); */
                /* abort(500, 'Internal Server Error'); */
                throw new \LogicException("Database driver ($driver) not supported");
            }
        }
    }

    public function scopeOrdered(Builder $query, ?string $orderMethod) {
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
