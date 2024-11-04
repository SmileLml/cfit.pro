<?php
/**
 * The model file of api module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id$
 * @link        http://www.zentao.net
 */
include 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class mobileapiModel extends model
{
    /**
     * Get the details of the method by file path.
     *
     * @param  string $filePath
     * @param  string $ext
     * @access public
     * @return object
     */
    public function getMethod($filePath, $ext = '')
    {
        $fileName  = dirname($filePath);
        $className = basename(dirname(dirname($filePath)));
        if(!class_exists($className)) helper::import($fileName);
        $methodName = basename($filePath);

        $method = new ReflectionMethod($className . $ext, $methodName);
        $data   = new stdClass();
        $data->startLine  = $method->getStartLine();
        $data->endLine    = $method->getEndLine();
        $data->comment    = $method->getDocComment();
        $data->parameters = $method->getParameters();
        $data->className  = $className;
        $data->methodName = $methodName;
        $data->fileName   = $fileName;
        $data->post       = false;

        $file = file($fileName);
        for($i = $data->startLine - 1; $i <= $data->endLine; $i++)
        {
            if(strpos($file[$i], '$this->post') or strpos($file[$i], 'fixer::input') or strpos($file[$i], '$_POST'))
            {
                $data->post = true;
            }
        }
        return $data;
    }

    /**
     * Request the api.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  string $action
     * @access public
     * @return array
     */
    public function request($moduleName, $methodName, $action)
    {
        $host  = common::getSysURL();
        $param = '';
        if($action == 'extendModel')
        {
            if(!isset($_POST['noparam']))
            {
                foreach($_POST as $key => $value) $param .= ',' . $key . '=' . $value;
                $param = ltrim($param, ',');
            }
            $url  = rtrim($host, '/') . inlink('getModel',  "moduleName=$moduleName&methodName=$methodName&params=$param", 'json');
            $url .= $this->config->requestType == "PATH_INFO" ? '?' : '&';
            $url .= $this->config->sessionVar . '=' . session_id();
        }
        else
        {
            if(!isset($_POST['noparam']))
            {
                foreach($_POST as $key => $value) $param .= '&' . $key . '=' . $value;
                $param = ltrim($param, '&');
            }
            $url  = rtrim($host, '/') . helper::createLink($moduleName, $methodName, $param, 'json');
            $url .= $this->config->requestType == "PATH_INFO" ? '?' : '&';
            $url .= $this->config->sessionVar . '=' . session_id();
        }

        /* Unlock session. After new request, restart session. */
        session_write_close();
        $content = file_get_contents($url);
        session_start();

        return array('url' => $url, 'content' => $content);
    }

    /**
     * Query sql.
     *
     * @param  string    $sql
     * @param  string    $keyField
     * @access public
     * @return array
     */
    public function sql($sql, $keyField = '')
    {
        if(!$this->config->features->apiSQL) return sprintf($this->lang->api->error->disabled, '$config->features->apiSQL');

        $sql = trim($sql);
        if(strpos($sql, ';') !== false) $sql = substr($sql, 0, strpos($sql, ';'));

        $result = array();
        $result['status']  = 'fail';
        $result['message'] = '';

        if(empty($sql)) return $result;

        if(stripos($sql, 'select ') !== 0)
        {
            $result['message'] = $this->lang->api->error->onlySelect;
            return $result;
        }
        else
        {
            try
            {
                $stmt = $this->dbh->query($sql);

                $rows = array();
                if(empty($keyField))
                {
                    $rows = $stmt->fetchAll();
                }
                else
                {
                    while($row = $stmt->fetch()) $rows[$row->$keyField] = $row;
                }

                $result['status'] = 'success';
                $result['data']   = $rows;
            }
            catch(PDOException $e)
            {
                $result['status']  = 'fail';
                $result['message'] = $e->getMessage();
            }

            return $result;
        }
    }

    /* 返回响应信息。*/
    public function response($result = 'fail', $message = '', $data = array(), $logID = 0, $code = 0,$func = '')
    {
        $response = array('result' => $result, 'message' => $message, 'data' => $data,  'code' => $code);
       // $this->updateRequestLog($logID, $response);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        $this->saveLog($response,'mobileapi',$func);
        die();
    }

    /**
     * 生成token
     * @param $data
     * @return mixed
     */
    function createToken($data ,$refreshtoken = null)
    {
        //$key   = 'e10adc3949ba59abbe56e057f20f883e'; // 自定义秘钥，加密解密都需要用到。
        $time  = time(); // 当前时间戳。
        $token = [
            'iss'  => 'zentao', // 签发者。
            'aud'  => 'http://dpmp.cfit.cn', // 接收者。
            'iat'  => $time, // 签发时间。
            'nbf'  => $time, // (Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用。
            //'exp'  => $time + 7200, // 过期时间，不设置则永久有效。
            'data' => $data
            /*'data' => [
                'userid'   => 'a0001',
                'username' => '张三',
                'level'    => '高级用户',
                'avatar'   => 'https://www.baidu.com/img/PCtm_d9c8750bed0b3c7d089fa7d55720d6cf.png',
            ]*/
        ];
        $access_token = $token;
        $access_token['scopes'] = 'role_access';//token标识，请求接口的token
        $access_token['exp']    = $time + 7200; //access_token过期时间，设置2小时

        $refresh_token = $token;
        $refresh_token['scopes'] = 'role_refresh';//token标识，刷新access_token
        $refresh_token['exp']    = $time + 86400; //refresh_token过期时间，设置24小时

        //刷新token
        if($refreshtoken){
            $decoded = JWT::decode($refreshtoken, new Key($this->lang->api->key, 'HS256'));
        }

        $list = [
            'access_token'  => JWT::encode($access_token, $this->lang->api->key, 'HS256'),
            'refresh_token' => isset($decoded) && $decoded->scopes == 'role_refresh' ? $refreshtoken  : JWT::encode($refresh_token, $this->lang->api->key, 'HS256'),
        ];
        //$jwtToken = JWT::encode($token, $this->lang->api->key, 'HS256');
        return $list;
    }

    /**
     * 解析token
     * @param $jwtToken
     * @return mixed
     */
    function decodeToken()
    {
        $key   = $this->lang->api->key;
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
        }
//        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ6ZW50YW8iLCJhdWQiOiJodHRwOi8vZHBtcC5jZml0LmNuIiwiaWF0IjoxNzAzNTUzNzI1LCJuYmYiOjE3MDM1NTM3MjUsImRhdGEiOnsidWlkIjoiMTQiLCJ0eXBlIjoiaW5zaWRlIiwiZGVwdCI6IjEiLCJkZXB0TmFtZSI6Ilx1NGVhN1x1NTRjMVx1NTIxYlx1NjViMFx1OTBlOCIsImFjY291bnQiOiJsaXRpYW56aSIsInJlYWxuYW1lIjoiXHU2NzRlXHU3NTFjXHU2ODkzIn0sInNjb3BlcyI6InJvbGVfYWNjZXNzIiwiZXhwIjoxMTEyODE0NjcwOTI1fQ.RJ8PMw_ftcuaASHjJTJW38R3jjRAUyrlV3JdaGPCCaw';
        if (isset($token) && $token) {
            try {
                JWT::$leeway += 60;
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                if($decoded->scopes == 'role_access'){
                    return $decoded;
                    //$this->response('success',$this->lang->api->successful, $data = array('user' => $decoded->data),  0, 200);
                }else{
                    $this->response('fail','token error',array(),0,401);//其他错误
                }
            } catch (\Firebase\JWT\SignatureInvalidException $e) {
               $this->response('fail',$e->getMessage(),array(),0,401);//签名不正确
            }catch(\Firebase\JWT\BeforeValidException $e){
                $this->response('fail',$e->getMessage(),array(),0,401);//签名再某个时间点之后才能用
            }catch (\Firebase\JWT\ExpiredException $e){
                $this->response('fail',$e->getMessage(),array(),0,201);//token过期
            }catch(Exception $e){
                $this->response('fail',$e->getMessage(),array(),0,401);//其他错误
            }
        } else {
            $this->response('fail','No token provided',array(),0,401);//其他错误
        }
    }

    /**
     * 解析刷新refreshtoken
     * @param $jwtToken
     * @return mixed
     */
    function decodeRefreshToken()
    {
        $key   = $this->lang->api->key;
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
        }
        if (isset($token) && $token) {
            try {
                JWT::$leeway += 60;
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                if($decoded->scopes == 'role_refresh'){
                    $tokenList =  $this->createToken($decoded->data,$token);
                    return $tokenList;
                }else{
                    $this->response('fail','token error',array(),0,401);//其他错误
                }

            } catch (\Firebase\JWT\SignatureInvalidException $e) {
                $this->response('fail',$e->getMessage(),array(),0,401);//签名不正确
            }catch(\Firebase\JWT\BeforeValidException $e){
                $this->response('fail',$e->getMessage(),array(),0,401);//签名再某个时间点之后才能用
            }catch (\Firebase\JWT\ExpiredException $e){

                $this->response('fail',$e->getMessage(),array(),0,206);//token过期
            }catch(Exception $e){
                $this->response('fail',$e->getMessage(),array(),0,401);//其他错误
            }
        } else {
            $this->response('fail','No token provided',array(),0,401);//其他错误
        }
    }

    /**
     * 根据token获取用户信息
     * @return mixed
     */
    function getUser()
    {
        $key = $this->lang->api->key;
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
        }

        if ($token) {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->data;
        }
    }

    /**
     * 记录日志
     * @param $line
     */
    function saveLog($line, $model = 'mobileapi', $func = 'run'){
        if(is_array($line) || is_object($line))
        {
            $line = json_encode($line, JSON_UNESCAPED_UNICODE);
        }
        $line = '['.date('H:i:s').']-'. $model .'-'. $func . ':: '.$line .PHP_EOL;
        $logPath = $_SERVER['DOCUMENT_ROOT'].'/data/mobileapilog/'.date('Ym').'/';
        if(!is_dir($logPath)) mkdir($logPath, 0777, true);
        $logFile = $logPath.'mobileapi-'.date('Y-m-d').'-'.$model.'-'.$func.'.log';
        file_put_contents($logFile, $line, FILE_APPEND);

    }

}
