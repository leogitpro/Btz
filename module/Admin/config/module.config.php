<?php
/**
 * Module configuration
 */

namespace Admin;

return [
    'router' => require(__DIR__ . '/module.router.php'),
    'controllers' => require(__DIR__ . '/module.controller.php'),
    'view_manager' => require(__DIR__ . '/module.view.php'),
];