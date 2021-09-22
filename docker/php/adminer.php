<?php

function adminer_object(): Adminer {
    return new class extends Adminer {
        public function login($login, $password): bool {
            return true;
        }
    };
}

require __DIR__ . '/adminer.dist.php';
