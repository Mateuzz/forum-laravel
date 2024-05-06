<?php

namespace App\Lib;

class FullText {
    public static function transformSearch(string $s): string {
        $words = explode(' ', $s);
        $result = '';
        $firstLoop = true;

        foreach ($words as $word) {
            if ($firstLoop) {
                $firstLoop = false;
                $result .= "$word:*";
            } else {
                $result .= " & $word:*";
            }
        }

        return $result;
    }
}


?>
