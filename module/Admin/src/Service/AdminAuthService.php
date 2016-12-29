<?php
/**
 * Custom administrator authentication service
 *
 * User: leo
 */

namespace Admin\Service;


use Zend\Authentication\AuthenticationService;


/**
 * Class AdminAuthService
 * Avoid the default authentication service use same name.
 *
 * @package Admin\Service
 */
class AdminAuthService extends AuthenticationService
{

}