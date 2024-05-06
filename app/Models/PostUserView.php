<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon last_view_date
 * @property string user_cookie_identifier
 * @property string user_ip_identifier
 * @property int id
 * @property int post_id
 * */
class PostUserView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array {
        return ['last_view_date' => 'datetime'];
    }
}
