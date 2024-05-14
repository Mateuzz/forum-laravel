<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property Carbon last_view_date
 * @property string user_cookie_identifier
 * @property string user_ip_identifier
 * @property int id
 * @property int post_id
 * @mixin IdeHelperPostUserView
 * @property int $id
 * @property int $post_id
 * @property string $user_cookie_identifier
 * @property string $user_ip_identifier
 * @property \Illuminate\Support\Carbon $last_view_date
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView whereLastViewDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView whereUserCookieIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostUserView whereUserIpIdentifier($value)
 * @method static \Database\Factories\PostUserViewFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class PostUserView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array {
        return ['last_view_date' => 'datetime'];
    }
}
