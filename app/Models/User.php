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

    public function scopeFiltered(Builder $query, array $filters) {
        /* if (isset($filters['search'])) { */
        /*     $s = strtolower($filters['search']); */
        /*     $query->whereRaw("lower(username) like '%$s%'"); */
        /* } */

        if (isset($filters['search'])) {
            $s = FullText::transformSearch($filters['search']);
            $query->whereRaw("search @@ to_tsquery('$s')");
        }
    }

    public function scopeOrdered(Builder $query, ?string $orderMethod) {
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
