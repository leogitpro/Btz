<?php
/**
 * Email address is existed validator
 *
 * User: leo
 */

namespace User\Validator;


use User\Entity\User;
use Zend\Validator\AbstractValidator;

class EmailExistedValidator extends AbstractValidator
{

    /**
     * @var array
     */
    protected $options = [
        'entityManager' => null,
    ];


    // Message IDs
    const EMAIL_NO_EXISTS = 'emailNoExist';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::EMAIL_NO_EXISTS => 'The E-mail is exists other world!',
    ];


    /**
     * EmailExistedValidator constructor.
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['entityManager'])) {
                $this->options['entityManager'] = $options['entityManager'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the email is existed.
     * if email has existed return true. else return false.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        // The entityManager
        $entityManager = $this->options['entityManager'];

        // Find user by the email value, If $existedUser is null => no user use the email.
        $user = $entityManager->getRepository(User::class)->findOneByEmail($value);

        if (null == $user) {
            $this->error(self::EMAIL_NO_EXISTS);
            return false;
        }

        return true;
    }

}