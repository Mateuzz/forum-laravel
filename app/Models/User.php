<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Lib\FullText;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

enum UserOrderTypes: string {
    case Latest = 'latest';
    case Oldest = 'oldest';
    case PostsPublished = 'posts-published';
    case ViewsReceived = 'views-received';
    case DonationsGiven = 'donations-given';
}

enum UserStatus: string {
    case Online = 'online';
    case Offline = 'offline';
}

enum UserType: string {
    case Admin = 'admin';
    case User = 'user';
}

/**
 *
 *
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $username
 * @property string $email
 * @property mixed $password
 * @property string|null $image
 * @property \App\Models\UserStatus $status
 * @property \App\Models\UserType $type
 * @property string $donations
 * @property int $posts_published
 * @property int $posts_views_received
 * @property int $stars_received
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $last_activity
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $search
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User filtered(array $filters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User ordered(?string $orderMethod)
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDonations($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereImage($value)
 * @method static Builder|User whereLastActivity($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePostsPublished($value)
 * @method static Builder|User wherePostsViewsReceived($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereSearch($value)
 * @method static Builder|User whereStarsReceived($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User filter(array $filters)
 * @method static Builder|User order(?string $orderMethod)
 * @mixin \Eloquent
 */
class User extends Authenticatable {
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    const ALLOWED_ORDER_TYPES = ['latest', 'oldest', 'posts-published', 'views-received', 'donations-given'];

    const MAX_USERNAME_SIZE = 255;
    const MAX_EMAIL_SIZE = 255;
    const MAX_IMAGE_URL_SIZE = 255;

    const MIN_USERNAME_SIZE = 3;
    const MIN_PASSWORD_SIZE = 14;

    public function scopeFilter(Builder $query, array $filters) {
        /* if (isset($filters['search'])) { */
        /*     $s = strtolower($filters['search']); */
        /*     $query->whereRaw("lower(username) like '%$s%'"); */
        /* } */

        if (isset($filters['search'])) {
            $s = FullText::transformSearch($filters['search']);
            $query->whereRaw("search @@ to_tsquery('$s')");
        }
    }

    public function scopeOrder(Builder $query, ?string $orderMethod) {
        switch (UserOrderTypes::from($orderMethod ?? 'latest')) {
        case UserOrderTypes::Latest:
            $query->latest();
            break;

        case UserOrderTypes::Oldest:
            $query->oldest();
            break;

        case UserOrderTypes::PostsPublished:
            $query->orderBy('posts_published', 'desc');
            break;

        case UserOrderTypes::ViewsReceived:
            $query->orderBy('posts_views_received', 'desc');
            break;

        case UserOrderTypes::DonationsGiven:
            $query->orderBy('donations', 'desc');
            break;
        }
    }

    public function posts() {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     *
     * @var array<int, string>
     */ protected $hidden = [
        'password',
        'remember_token',
        'search',
        'email_verified_at',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'type' => UserType::class,
        ];
    }
}
