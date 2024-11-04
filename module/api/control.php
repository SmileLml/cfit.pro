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
class api extends control
{
    static $_statusList = [
        '701' => 'filename error',  //下载文件名错误
        '702' => 'sign error',      //签名错误
        '703' => 'download exists', //其他进程正在下载此文件
    ];
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
}
