<?php
/**
 * Custom active code validator
 *
 * User: leo
 */

namespace User\Validator;


use User\Entity\User;
use Zend\Validator\AbstractValidator;

class ActiveCodeValidator extends AbstractValidator
{

    private $options = [
        'entityManager' => null,
    ];


    // Message IDs
    const CODE_INVALID = 'codeInvalid';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::CODE_INVALID => 'The code is invalid.',
    ];



    /**
     * ActiveCodeValidator constructor.
     * @param null $options
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
        $code = (string)$value;

        // The entityManager
        $entityManager = $this->options['entityManager'];

        $user = $entityManager->getRepository(User::class)->findOneByActiveToken($code);

        if (null == $user) {
            $this->error(self::CODE_INVALID);
            return false;
        }

        if(User::STATUS_ACTIVE == $user->getStatus()) {
            $this->error(self::CODE_INVALID);
            return false;
        }

        return true;

    }



}