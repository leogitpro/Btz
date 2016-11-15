<?php

namespace Admin;

return [
    'template_map' => [
        'layout/admin_simple'           => __DIR__ . '/../view/layout/simple.phtml',
        'layout/admin_default'           => __DIR__ . '/../view/layout/layout.phtml',
    ],
    'template_path_stack' => [
        __DIR__ . '/../view',
    ],
];
