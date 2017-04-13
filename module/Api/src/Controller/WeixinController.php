<?php
/**
 * WeixinController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api\Controller;


use Api\Exception\InvalidArgumentException as ApiInvalidArgumentException;


class WeixinController extends ApiBaseController
{

    /**
     * 获取公众号 AccessToken
     */
    public function tokenAction()
    {
        $weixinId = (int)$this->params()->fromRoute('key', 0);
        if (!$weixinId) {
            throw new ApiInvalidArgumentException('无效的公众号ID');
        }
    }
}