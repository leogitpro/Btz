<?php
/**
 * WeChatAppIdUniqueValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\WeChat;
use Admin\Service\WeChatManager;
use Zend\Validator\AbstractValidator;


class WeChatAppIdUniqueValidator extends AbstractValidator
{

    const APPID_EXISTED = 'appIdExisted';

    protected $options = [
        'weChatManager' => null,
        'weChat' => null,
    ];

    protected $messageTemplates = [
        self::APPID_EXISTED => '此微信 AppID 已经被使用了. 请确认输入正确.',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['weChatManager'])) {
                $this->options['weChatManager'] = $options['weChatManager'];
            }
            if (isset($options['weChat'])) {
                $this->options['weChat'] = $options['weChat'];
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

        $wm = $this->options['weChatManager'];
        $weChat = $this->options['weChat'];

        if (!$wm instanceof WeChatManager) {
            $this->error(self::APPID_EXISTED);
            return false;
        }

        $count = $wm->getWeChatCountByAppId($value);

        if (!$weChat instanceof WeChat) { // Created validate
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
                    if($value == $weChat->getWxAppId()) {
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