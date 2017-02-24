<?php
/**
 * WechatAppIdUniqueValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\Wechat;
use Admin\Service\WechatManager;
use Zend\Validator\AbstractValidator;

class WechatAppIdUniqueValidator extends AbstractValidator
{

    const APPID_EXISTED = 'appIdExisted';

    protected $options = [
        'wechatManager' => null,
        'wechat' => null,
    ];

    protected $messageTemplates = [
        self::APPID_EXISTED => '此微信 AppID 已经被使用了. 请确认输入正确.',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['wechatManager'])) {
                $this->options['wechatManager'] = $options['wechatManager'];
            }
            if (isset($options['wechat'])) {
                $this->options['wechat'] = $options['wechat'];
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

        $wechatManager = $this->options['wechatManager'];
        $wechat = $this->options['wechat'];

        if (!$wechatManager instanceof WechatManager) {
            $this->error(self::APPID_EXISTED);
            return false;
        }

        $count = $wechatManager->getWechatCountByAppId($value);

        if (null == $wechat) { // Created validate
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
                    if (!$wechat instanceof Wechat) {
                        $this->error(self::APPID_EXISTED);
                        return false;
                    }

                    if($value == $wechat->getWxAppId()) {
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