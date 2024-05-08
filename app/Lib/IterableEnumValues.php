<?php

namespace App\Lib;

trait IterableEnumValues {
    static private function getValues(): array {
        return array_map(fn($val) => $val->value, self::cases());
    }
}

?>
