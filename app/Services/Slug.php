<?php

namespace App\Services;

class Slug {
    static public function fromTitle(string $title): string {
        return strtolower(preg_replace('/\s+/', '-', trim($title)));
    }

    static public function createUnique(string $title, string $model): string {
        $slug = self::fromTitle($title);
        $postWithSameSlug = $model::where('slug', $slug)->first();

        if ($postWithSameSlug) {
            $slug = $slug . '.' . bin2hex(random_bytes(16));
        }

        return $slug;
    }
}

?>
