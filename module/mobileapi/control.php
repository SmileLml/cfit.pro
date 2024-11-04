<?php
/**
 * The control file of api of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id: control.php 5143 2013-07-15 06:11:59Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
class mobileapi extends control
{
    static $_statusList = [
        '701' => 'filename error',  //下载文件名错误
        '702' => 'sign error',      //签名错误
        '703' => 'download exists', //其他进程正在下载此文件
    ];

    public function __construct($moduleName = '', $methodName = '', $appName = '')
    {
        parent::__construct($moduleName, $methodName, $appName);

        //允许所有域名访问
        header('Access-Control-Allow-Origin:*');
        //响应类型
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
        //带cookie的跨域访问
        header('Access-Control-Allow-Credentials:true');
        //响应头设置
        //header('Access-Control-Allow-Headers:x-requested-with,Content-Type,X-CSRF-Token,Authorization');
        header('Access-Control-Allow-Headers:*');
        header('Access-Control-Request-Headers:*');
        //global $app;
        //$moduleName = $app->rawModule;
        $methodName = $this->app->rawMethod;

        if ($methodName == 'gettesttoken'){
            $account = $_GET['user'];
            $module = isset($_GET['type']) ? $_GET['type'] : 'modify';
            $user = $this->dao->select('*')->from(TABLE_USER)->where('ldap')->eq($account)->fetch();
            $user = $this->loadModel('user')->identify($user->account, '12345678', $user, true);
            if($user)
            {
                $ip   = $this->server->remote_addr;
                $last = $this->server->request_time;

                $user->lastTime = $user->last;
                $user->last     = date(DT_DATETIME1, $last);
                $user->admin    = strpos($this->app->company->admins, ",{$user->account},") !== false;
                $user->ip       = $ip;

                $this->loadModel('user')->login($user);


                $dept = $this->loadModel('dept')->getByID($user->dept);
                $data = new stdClass();
                $data->uid  = $user->id;
                $data->type = $user->type;
                $data->dept = $user->dept;
                $data->deptName = isset($dept->name) ? $dept->name : '';
                $data->account = $user->account;
                $data->realname = $user->realname;
                $this->session->set('user', $user);
                $this->app->user = $user;
                $token =  $this->loadModel('mobileapi')->createToken($data);//生成token
                //$this->loadModel('mobileapi')->response('success', $this->lang->api->successful, $data = array('token' =>$token,'user' => $data,'test' =>$this->lang->api->serviceUrl."?token=".$token['access_token'].";".$token['refresh_token']),  0, 200);
                //测试
                $module = 'waitdeal';
//                a($token['access_token']);exit;
                $this->locate('http://localhost:8080/#/home?type='.$module."&token=".$token['access_token'].";".$token['refresh_token']);
                $this->locate($this->lang->api->h5Url.'#/home?type='.$module."&token=".$token['access_token'].";".$token['refresh_token']);
//                $this->locate($this->lang->api->h5Url.'#/home?module='.$module."&token=".$token['access_token'].";".$token['refresh_token']);
                //正式
//            $this->locate(urldecode( $redirect)."&token=".$token['access_token'].";".$token['refresh_token']);
            }
            exit;
        }
        //检查用户是否登录
        if($methodName != 'loginapi' && $methodName != 'refreshtokenapi' && $methodName !='tokenloginapi' && $methodName != 'getwaitnumapi'){
            $token = $this->loadModel('mobileapi')->decodeToken();
            if(!isset($token->data) && !isset($token->data->uid)){
                return false;
            }
            $user = $this->loadModel('mobileapi')->getUser();
            $this->app->user = $user;
            // 所属产品用到 查询产品列表
            $rights = $this->loadModel('user')->authorize($this->app->user->account);
            $this->app->user->view   = $this->user->grantUserView($this->app->user->account, $rights['acls']);
        }
    }



    /**
     * Return session to the client.
     *
     * @access public
     * @return void
     */
    public function getSessionID()
    {
        $this->session->set('rand', mt_rand(0, 10000));
        $this->view->sessionName = session_name();
        $this->view->sessionID   = session_id();
        $this->view->rand        = $this->session->rand;
        $this->display();
    }

    /**
     * Execute a module's model's method, return the result.
     *
     * @param  string    $moduleName
     * @param  string    $methodName
     * @param  string    $params        param1=value1,param2=value2, don't use & to join them.
     * @access public
     * @return string
     */
    public function getModel($moduleName, $methodName, $params = '')
    {
        if(!$this->config->features->apiGetModel) die(sprintf($this->lang->api->error->disabled, '$config->features->apiGetModel'));

        $params    = explode(',', $params);
        $newParams = array_shift($params);
        foreach($params as $param)
        {
            $sign = strpos($param, '=') !== false ? '&' : ',';
            $newParams .= $sign . $param;
        }

        parse_str($newParams, $params);
        $module = $this->loadModel($moduleName);
        $result = call_user_func_array(array(&$module, $methodName), $params);
        if(dao::isError()) die(json_encode(dao::getError()));
        $output['status'] = $result ? 'success' : 'fail';
        $output['data']   = json_encode($result);
        $output['md5']    = md5($output['data']);
        $this->output     = json_encode($output);
        die($this->output);
    }

    /**
     * The interface of api.
     *
     * @param  int    $filePath
     * @param  int    $action
     * @access public
     * @return void
     */
    public function debug($filePath, $action)
    {
        $filePath = helper::safe64Decode($filePath);
        if($action == 'extendModel')
        {
            $method = $this->api->getMethod($filePath, 'Model');
        }
        elseif($action == 'extendControl')
        {
            $method = $this->api->getMethod($filePath);
        }

        if(!empty($_POST))
        {
            $result  = $this->api->request($method->className, $method->methodName, $action);
            $content = json_decode($result['content']);
            $status  = $content->status;
            $data    = json_decode($content->data);
            $data    = '<xmp>' . print_r($data, true) . '</xmp>';

            $response['result'] = 'success';
            $response['status'] = $status;
            $response['url']    = $result['url'];
            $response['data']   = $data;
            $this->send($response);
        }

        $this->view->method   = $method;
        $this->view->filePath = $filePath;
        $this->display();
    }

    /**
     * Query sql.
     *
     * @param  string $keyField
     * @access public
     * @return void
     */
    public function sql($keyField = '')
    {
        if(!$this->config->features->apiSQL) die(sprintf($this->lang->api->error->disabled, '$config->features->apiSQL'));

        $sql    = isset($_POST['sql']) ? $this->post->sql : '';
        $output = $this->api->sql($sql, $keyField);

        $output['sql'] = $sql;
        $this->output  = json_encode($output);
        die($this->output);
    }

    /**
     * 接口验证token
     * @param $queryString
     */
    public function checkApiToken()
    {
        parse_str($this->server->query_String, $queryString);
        if(empty($queryString['time']) || empty($queryString['token']) || empty($queryString['code']))
        {
            $this->response('PARAMS_ERROR');
        }
        $this->loadModel('entry');
        $entry = $this->entry->getByCode($queryString['code']);
        $timestamp = $queryString['time'];
        if(abs(time()-$timestamp) > 3600) $this->response('TIMESTAMP_EXPIRED'); //todo 一小时过期
        $result = $queryString['token'] == md5($entry->code . $entry->key . $queryString['time']);
        if($result == false) $this->response('TOKEN_ERROR');
    }

    /**
     * 获取测试token
     */
    public function getTestToken()
    {
        parse_str($this->server->query_String, $queryString);
        $this->loadModel('entry');
        $entry = $this->entry->getByCode($queryString['code']);
        die("code:".$entry->code."<br>key:".$entry->key."<br>time:".time().'<br>token:'.md5($entry->code . $entry->key . time())); //获取测试token
    }
    /**
     * Response.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function response($code, $status = '')
    {
        $response = new stdclass();
        $response->errcode = $code;
        if($status && is_numeric($status)){
            header('HTTP/1.1 '. $status .' '. self::$_statusList[$status] ) ;
        }
        die(helper::jsonEncode($response));
    }

    public function success($code, $data)
    {
        $response = new stdclass();
        $response->code = $code;
        $response->data = $data;
        die(helper::jsonEncode($response));
    }
    // 待办、已办列表
    public function waitDealList(){
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('modify');
        $this->app->loadLang('sectransfer');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('problem');
        $this->app->loadLang('info');
        $this->app->loadLang('putproduction');
        $this->app->loadLang('requirement');
        $this->app->loadLang('infoqz');
        $this->app->loadLang('credit');
        $this->app->loadLang('change');
        $mobileConfig = $this->config->mobileapi;

        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 10;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $type   = isset($_POST['type']) ? $_POST['type'] : 1; //已办、待办
        $objectType   = isset($_POST['objectType']) ? $_POST['objectType'] : ''; //已办、待办
        $depts   = $this->loadModel('dept')->getOptionMenu();

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $res = $this->dao->select("*")->from(TABLE_NEED_DEAL_MESSAGE)
            ->where('deleted')->eq(0)
            ->andWhere('`status`')->in($type)
            ->andWhere('reviewer')->eq($this->app->user->account)
            ->beginIF($objectType != '')->andwhere("objectType")->in($objectType)->fi()
            ->beginIF($search != '')->andWhere()->fi()
            ->beginIF($search != '')->markleft(1)->fi()
            ->beginIF($search != '')->Where('code')->like("%".$search."%")->fi()
            ->beginIF($search != '')->orWhere('`desc`')->like("%".$search."%")->fi()
            ->beginIF($search != '')->markright(1)->fi()
//            ->beginIF($search != '')->andwhere(" ( `code` like '%$search%' or `desc` like '%$search%' )")->fi()
            ->orderBy('id_desc')
            ->page($pager)
            ->fetchall();

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $userMobiles = $this->loadModel('user')->getUserMobileList();
        $list = [];
        foreach ($res as $k => $v){
            $info = $this->dao->select($mobileConfig->objectFlieldsList[$v->objectType])->from($mobileConfig->objectTableList[$v->objectType])->where('id')->eq($v->objectId)->fetch();
            $obj = new stdClass();
            $obj->user        = $users[$v->formCreatedBy];
            $obj->planBegin        = '';
            $obj->planEnd          = '';
            $obj->phone            = isset($userMobiles[$v->formCreatedBy]) ? $userMobiles[$v->formCreatedBy] : '';
            $obj->phone            = isset($info->applyUsercontact) && $info->applyUsercontact ? $info->applyUsercontact : $obj->phone;
            if ($v->objectType == 'modify'){
                $obj->planBegin        = $info->planBegin;
                $obj->planEnd          = $info->planEnd;
                $obj->type             = $this->lang->modifycncc->typeList[$info->type];
            }
            if ($v->objectType == 'outwarddelivery' && $info->isNewModifycncc == 1){
                $modifycncc = $this->dao->select('planEnd,planBegin,type')->from(TABLE_MODIFYCNCC)->where('id')->eq($info->modifycnccId)->fetch();
                $obj->planBegin        = $modifycncc->planBegin;
                $obj->planEnd          = $modifycncc->planEnd;
                $obj->type             = $this->lang->modifycncc->typeList[$modifycncc->type];
            }
            if ($v->objectType == 'problem'){
                $obj->acceptUser          = zget($users, $info->acceptUser, '');
                // 受理部门
                $obj->acceptDept          = zget($depts, $info->acceptDept, '');
                /**@var userModel $userModel */
//                $userModel = $this->loadModel('user');
//                $userInfo = $userModel->getById($info->acceptUser);
//                $obj->phone            = $userInfo->mobile;
                $obj->phone            = isset($userMobiles[$info->acceptUser]) ? $userMobiles[$info->acceptUser] : '';
                $v->formCreatedDate    = date("Y-m-d",strtotime($v->formCreatedDate));
            }
            if($v->objectType == 'infoqz'){
                $obj->phone            = $info->createUserPhone;
            }
            if ($v->objectType == 'requirement'){
                $obj->phone            = isset($userMobiles[$info->feedbackBy]) ? $userMobiles[$info->feedbackBy] : '';
                $obj->acceptUser       = $users[$info->feedbackBy];
                $obj->end              = $info->end;
            }
//            if($v->objectType == 'putproduction'){
//                $userModel = $this->loadModel('user');
//                $userInfo = $userModel->getById($info->createdBy);
//                $obj->phone            = $userInfo->mobile;
//            }
            $obj->statusName  = $this->lang->{$v->objectType}->{$mobileConfig->objectStatusList[$v->objectType]}[$info->status];
            $obj->id          = $v->objectId;
            $obj->code        = $v->code;
//            $obj->desc        = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($v->desc,ENT_QUOTES)))));//富文本
            $obj->desc        = html_entity_decode(strip_tags(str_replace("<br />","",strval(htmlspecialchars_decode($info->desc,ENT_QUOTES)))));//富文本
            $obj->createdDate = $v->createdDate;
            $obj->formCreatedDate = date("Y-m-d",strtotime($v->formCreatedDate));
            $obj->updatedDate = $v->updatedDate;
            $obj->updatedDateYear = date("Y-m-d",strtotime($v->updatedDate));
//            $obj->extra       = json_decode($v->extra);
            $obj->objectType     = $v->objectType;
            $list[]       = $obj;
        }
        $data = new stdClass();
        $data->list       = $list;
        $data->recTotal   = $pager->recTotal;
        $data->recPerPage = $pager->recPerPage;
        $data->pageID     = $pager->pageID;
        $data->pageTotal  = $pager->pageTotal;
        $this->loadModel('mobileapi')->response('success', '', $data ,  0, 200,'waitDealList');
    }
}
