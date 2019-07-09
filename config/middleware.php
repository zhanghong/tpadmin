<?php

return [
    'tpadmin.admin' => [
        \tpadmin\middleware\AuthCheck::class,
    ],
    'tpadmin.admin.role' => [
        \tpadmin\middleware\RoleCheck::class,
    ],
];
