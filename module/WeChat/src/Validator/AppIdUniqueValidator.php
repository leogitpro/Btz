<?php
/**
 * AppIdUniqueValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Validator;


use WeChat\Entity\Account;
use WeChat\Service\AccountService;
use Zend\Validator\AbstractValidator;


class AppIdUniqueValidator extends AbstractValidator
{

    const APPID_EXISTED = 'appIdExisted';

    protected $options = [
        'accountService' => null,
        'account' => null,
    ];

    protected $messageTemplates = [
        self::APPID_EXISTED => '此微信 AppID 已经被使用了.',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['accountService'])) {
                $this->options['accountService'] = $options['accountService'];
            }
            if (isset($options['account'])) {
                $this->options['account'] = $options['account'];
            }
        }

        parent::__construct($options);
    }

    /**
     * Check the appid is unique.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {

        $accountService = $this->options['accountService'];
        $account = $this->options['account'];

        if (!$accountService instanceof AccountService) {
            $this->error(self::APPID_EXISTED);
            return false;
        }

        $count = $accountService->getWeChatCountByAppId($value);

        if (!$account instanceof Account) { // Created validate
            if ($count > 0) {
                $this->error(self::APPID_EXISTED);
                return false;
            }
            return true;
        } else { // Edit

            if ($count < 1) {
                return true;
            } else {
                if ($count > 1) {
                    $this->error(self::APPID_EXISTED);
                    return false;
                } else {
                    if($value == $account->getWxAppId()) {
                        return true;
                    } else {
                        $this->error(self::APPID_EXISTED);
                        return false;
                    }
                }
            }
        }
    }

}