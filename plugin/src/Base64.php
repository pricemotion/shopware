<?php

namespace Pricemotion\Shopware\Util;

class Base64 {
    public static function encode(string $data): string {
        $result = base64_encode($data);
        $result = rtrim($result, '=');
        return strtr($result, [
            '+' => '-',
            '/' => '_',
        ]);
    }
}