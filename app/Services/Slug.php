<?php

namespace App\Services;

use App\Models\Post;

class Slug {
    static public function fromTitle(string $title): string {
        return strtolower(preg_replace('/\s+/', '-', trim($title)));
    }

    static public function getUniquePostSlug(string $title): string {
        $slug = self::fromTitle($title);
        $postWithSameSlug = Post::where('slug', $slug)->first();

        if ($postWithSameSlug) {
            $slug = $slug . '.' . bin2hex(random_bytes(16));
        }

        return $slug;
    }
}

?>
