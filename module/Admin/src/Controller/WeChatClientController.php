<?php
/**
 * WeChatClientController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Form\WeChatClientForm;
use Zend\View\Model\ViewModel;


/**
 * 公众号客户端管理
 *
 * Class WeChatClientController
 * @package Admin\Controller
 */
class WeChatClientController extends AdminBaseController
{

    /**
     * 客户端列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatClientService()->getClientCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatClient', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatClientService()->getClientsWithLimitPageByWeChat($weChat, $page, $size);

        return new ViewModel([
            'weChat' => $weChat,
            'apis' => (array)$this->getConfigPlugin()->get('api_list.weixin'),
            'clients' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 添加客户端
     */
    public function addAction()
    {

        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        if ($weChat->getClients()->count() > 9) {
            return $this->go(
                '客户端已经超额',
                '您的微信公众号访问客户端数量已经超额! 最多可以设置9个.',
                $this->url()->fromRoute('admin/weChatClient')
            );
        }

        $apis = (array)$this->getConfigPlugin()->get('api_list.weixin');

        $form = new WeChatClientForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                $start = new \DateTime($data['active']);
                $activeTime = $start->format('U') + 1;

                $end = new \DateTime($data['expire']);
                $end->modify("+1 day");
                $expireTime = $end->format('U') - 1;

                $selectedApis = (array)$data['apis'];
                $apiList = [];
                if(!empty($selectedApis)) {
                    foreach ($selectedApis as $_api) {
                        if (array_key_exists($_api, $apis)) {
                            $apiList[] = $_api;
                        }
                    }
                }

                //echo '<pre>'; print_r($apis); print_r($selectedApis); print_r($apiList); echo '</pre>';
                //**
                $this->getWeChatClientService()->createWeChatClient(
                    $weChat,
                    $data['name'],
                    $data['domain'],
                    $data['ip'],
                    implode(',', $apiList),
                    $activeTime,
                    $expireTime
                );

                return $this->go(
                    '客户端已经创建',
                    '您的微信公众号访问客户端已经创建成功!',
                    $this->url()->fromRoute('admin/weChatClient')
                );
                //*/
            }
        }

        return new ViewModel([
            'form' => $form,
            'apis' => $apis,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 删除客户端
     */
    public function deleteAction()
    {
        $clientId = (string)$this->params()->fromRoute('key');

        $client = $this->getWeChatClientService()->getWeChatClient($clientId);

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $client->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 你这是删除别人的客户端配置啊, 要逆天了!');
        }

        $name = $client->getName();

        $this->getWeChatClientService()->removeEntity($client);

        return $this->go(
            '客户端信息已经删除',
            '您的微信公众号访问客户端 ' . $name . ' 已经删除! 相关访问已经被禁止.',
            $this->url()->fromRoute('admin/weChatClient')
        );
    }


    /**
     * Api清单
     */
    public function apilistAction()
    {
        $clientId = (string)$this->params()->fromRoute('key');

        $client = $this->getWeChatClientService()->getWeChatClient($clientId);

        $weChat = $client->getWeChat();

        return new ViewModel([
            'client' => $client,
            'weChat' => $weChat,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 导出 Excel
     */
    public function exportAction()
    {
        $clientId = (string)$this->params()->fromRoute('key');

        $client = $this->getWeChatClientService()->getWeChatClient($clientId);

        $weChat = $client->getWeChat();

        $apis = $client->getApiList();
        $apiList = explode(',', $apis);

        $apiTpl = 'http://www.bentuzi.com/weixin/%s/' . $weChat->getWxId() . '/' . $client->getIdentifier();

        $apiUrls = [];

        if(in_array('oauth', $apiList)) {
            $apiUrls[] = [
                'title' => '网页授权接口',
                'url' => sprintf($apiTpl, 'oauth') . ".html?type=(base 或 userinfo)&url=urlencode('授权回调URL')",
            ];
        }

        if(in_array('jssign', $apiList)) {
            $apiUrls[] = [
                'title' => 'JSSDK签名授权接口',
                'url' => sprintf($apiTpl, 'jssign') . ".json?url=urlencode('需签名的URL')",
            ];
        }

        if(in_array('accesstoken', $apiList)) {
            $apiUrls[] = [
                'title' => '获取 AccessToken 接口',
                'url' => sprintf($apiTpl, 'accesstoken') . '.json',
            ];
        }

        if(in_array('jsapiticket', $apiList)) {
            $apiUrls[] = [
                'title' => '获取 JsApiTicket 接口',
                'url' => sprintf($apiTpl, 'jsapiticket') . '.json',
            ];
        }

        if(in_array('apiticket', $apiList)) {
            $apiUrls[] = [
                'title' => '获取 ApiTicket 接口',
                'url' => sprintf($apiTpl, 'apiticket') . '.json',
            ];
        }

        if(in_array('userinfo', $apiList)) {
            $apiUrls[] = [
                'title' => '获取用户信息接口',
                'url' => sprintf($apiTpl, 'userinfo') . '.json?openid=OPENID',
            ];
        }


        $excel = new \PHPExcel();
        $excel->getProperties()->setCreator($_SERVER['HTTP_HOST']);
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', '接口名')
            ->setCellValue('B1', '接口地址');
        $start = 2;
        foreach($apiUrls as $api) {
            $titleCell = 'A' . $start;
            $valueCell = 'B' . $start;
            $start++;
            $excel->setActiveSheetIndex(0)
                ->setCellValue($titleCell, $api['title'])
                ->setCellValue($valueCell, $api['url']);
        }

        $excel->setActiveSheetIndex(0)
            ->setCellValue('A' . $start, '接口文档')
            ->setCellValue('B' . $start, $this->url('app/index', ['action' => 'apidoc', 'suffix' => '.html']));

        $excel->getActiveSheet()->setTitle('微信接口列表');
        $excel->setActiveSheetIndex(0);

        $excelWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

        $filename = 'Api_list_' . $weChat->getWxId() . '_' . $client->getIdentifier() . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $excelWriter->save('php://output');

        return $this->getResponse();
    }



    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '公众号客户端', 'admin/weChatClient', 1, 'laptop', 21);

        $item['actions']['index'] = self::CreateActionRegistry('index', '客户端列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '添加客户端', 1, 'plus', 8);
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除客户端');
        $item['actions']['apilist'] = self::CreateActionRegistry('apilist', '客户端API列表');
        $item['actions']['export'] = self::CreateActionRegistry('export', ' 导出客户端API列表');

        return $item;
    }



}