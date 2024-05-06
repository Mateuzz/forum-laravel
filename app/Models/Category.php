<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
