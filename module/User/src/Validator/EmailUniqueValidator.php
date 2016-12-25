<?php
/**
 * User email unique validator
 *
 * @author: Leo
 */


namespace User\Validator;


use Zend\Validator\AbstractValidator;


class EmailUniqueValidator extends AbstractValidator
{

    /**
     * @var array
     */
    protected $options = [
        'userManager' => null,
        'user' => null,
    ];

    // Message IDs
    const NOT_SCALAR = 'notScalar';
    const USER_EXISTS = 'userExists';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_SCALAR => 'The email must be a scalar value',
        self::USER_EXISTS => 'Another user with such an email already exists',
    ];


    /**
     * UserExistsValidator constructor.
     *
     * @param array|mixed $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['userManager'])) {
                $this->options['userManager'] = $options['userManager'];
            }
            if (isset($options['user'])) {
                $this->options['user'] = $options['user'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the email is unique.
     * if email is unique return true. else return false.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_scalar($value)) { //Validate failure
            $this->error(self::NOT_SCALAR);
            return false;
        }


        $userManager = $this->options['userManager'];
        $existedUser = $userManager->getUserByEmail($value);

        $isValid = false;
        if (null == $this->options['user']) { // New created user email
            $isValid = (null == $existedUser);
        } else { // Existed user
            $oldEmail = $this->options['user']->getEmail();
            if($oldEmail == $value) { // No modify user self email
                $isValid = true;
            } else { // Modified the user email
                if (null != $existedUser) { // Other user used the email address
                    $isValid = false;
                } else {
                    $isValid = true;
                }
            }
        }

        if (!$isValid) {
            $this->error(self::USER_EXISTS);
        }

        return $isValid;
    }

}
