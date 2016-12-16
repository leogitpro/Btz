<?php
/**
 * Configuration user module controllers
 *
 */


namespace User;


use User\Controller\Factory\AuthControllerFactory;
use User\Controller\Factory\UserControllerFactory;

return [
    'factories' => [
        Controller\AuthController::class => AuthControllerFactory::class,
        Controller\ProfileController::class => UserControllerFactory::class,
    ],
];