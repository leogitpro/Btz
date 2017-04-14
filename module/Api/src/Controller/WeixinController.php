<?php
/**
 * WeixinController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api\Controller;


use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeixinController extends ApiBaseController
{

    /**
     * 获取公众号 AccessToken
     */
    public function tokenAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $data = ['success' => false, 'wxid' => 0];

        $weixinId = (int)$this->params()->fromRoute('key', 0);
        if (!$weixinId) {
            //todo
        }

        return new JsonModel($data);
    }


    public function authAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_HTML);

        return new ViewModel([
            'hello' => 'Wold',
        ]);
    }
}