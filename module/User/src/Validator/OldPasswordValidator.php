<?php
/**
 * Old password validator
 *
 * User: leo
 */

namespace User\Validator;


use Zend\Validator\AbstractValidator;


class OldPasswordValidator extends AbstractValidator
{

    // Message IDs
    const PWD_INVALID = 'pwdInvalid';


    /**
     * Validator options
     *
     * @var array
     */
    private $options = [
        'userManager' => null,
        'authService' => null,
    ];


    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::PWD_INVALID => 'The old password not matched.',
    ];


    /**
     * OldPasswordValidator constructor.
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['userManager'])) {
                $this->options['userManager'] = $options['userManager'];
            }
            if (isset($options['authService'])) {
                $this->options['authService'] = $options['authService'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Validate the value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $userManager = $this->options['userManager'];
        $authService = $this->options['authService'];
        $user = $userManager->getUserByEmail($authService->getIdentity());

        if (null == $user) {
            $this->error(self::PWD_INVALID);
            return false;
        }

        $password = $user->getPasswd();
        if($password != $value) {
            $this->error(self::PWD_INVALID);
            return false;
        }

        return true;
    }


}