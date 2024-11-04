<?php
set_time_limit(0); //php 不超时
date_default_timezone_set('PRC'); //设置时区

class DBbase{
    private static $connect;
    private static $sftpQz; //清总
    private static $sftpJX; //金信
    public static $_errCode;
    public static $_statusList = [
        'unavailable'   => 0,
        'ready'         => 1,
        'sending'       => 2,
        'success'       => 3,
        'transErr'      => 4, //对方验证MD5失败
        'networkErr'    => 5,
        'passwordErr'   => -1,
        'noFile'        => -2,
        'md5Err'        => -3,

    ];
    /**
     * @return mysqli|void
     */
    public static function getInstance($reconnetct = 0)
    {
        if(self::$connect && $reconnetct == 0) return self::$connect;
        $config = new stdClass();
        $config->timezone    = 'Asia/Shanghai';
//        $config->host        = '10.2.7.198';
//        $config->port        = '3306';
//        $config->name        = 'tongtest';
//        $config->user        = 'root';
//        $config->encoding    = 'UTF8';
//        $config->password    = '2wsx@WSX';
        $config->host        = '10.128.68.52';
        $config->port        = '3306';
        $config->name        = 'cfitpms';
        $config->user        = 'media';
        $config->encoding    = 'UTF8';
        $config->password    = 'media$cfit';
        $conn = new mysqli($config->host, $config->user, $config->password, $config->name);

        if ($conn->connect_error) {
            self::saveLog($conn->connect_error); //记录错误
            sleep(10); //10秒后重试
            die("mysql连接失败: " . $conn->connect_error);
        }
        return self::$connect = $conn;
    }

    /**
     * @return false|resource
     */
    public static function getSftpQz($reconnetct = 0)
    {
        if(self::$sftpQz && $reconnetct == 0) return self::$sftpQz; //每次新链接
        $sftpConfig['host']     = '172.22.140.219';
        $sftpConfig['port']     = '65222';
        $sftpConfig['username'] = 'jinke';
        $sftpConfig['password'] = 'sftp@2022';
        try {
            $conn = ssh2_connect($sftpConfig['host'], $sftpConfig['port']); //登陆远程服务器
        } catch (Exception $e){
            self::$_errCode = self::$_statusList['networkErr']; //连不上 网络原因 会重试
            return false;
        }
        if(!ssh2_auth_password($conn, $sftpConfig['username'], $sftpConfig['password'])) {
            self::saveLog('sftp连接失败'); //记录错误
            self::$_errCode = self::$_statusList['passwordErr']; //连不上 用户密码错误 不会重试
            return false;
            }
        return self::$sftpQz = ssh2_sftp($conn); //打开sftp
    }
    public static function getSftpJx()
    {
        if(self::$sftpJX) return self::$sftpJX;
        $sftpConfig['host']     = '172.22.140.219';
        $sftpConfig['port']     = '22';
        $sftpConfig['username'] = 'jinke';
        $sftpConfig['password'] = 'sftp@2022';
        try {
            $conn = ssh2_connect($sftpConfig['host'], $sftpConfig['port']); //登陆远程服务器
        } catch (Exception $e){
            self::$_errCode = self::$_statusList['networkErr']; //连不上 网络原因 会重试
            return false;
        }
        if(!ssh2_auth_password($conn, $sftpConfig['username'], $sftpConfig['password'])) {
            self::saveLog('sftp连接失败'); //记录错误
            self::$_errCode = self::$_statusList['passwordErr']; //连不上 用户密码错误 不会重试
            return false;
        }
        return self::$sftpJX = ssh2_sftp($conn); //打开sftp
    }
    /**
     * 记录日志
     */
    public static function saveLog($line, $model = 'cli', $func = 'run'){
        if(is_array($line) || is_object($line))
        {
            $line = json_encode($line, JSON_UNESCAPED_UNICODE);
        }
        $line = '['.date('H:i:s').']-'. $model .'-'. $func . ':: '.$line .PHP_EOL;
        $logPath = dirname(__FILE__).'/log/'.date('Ym').'/';
        if(!is_dir($logPath)) mkdir($logPath, 0777, true);
        $logFile = $logPath.'cron-'.date('Y-m-d').'-'.$model.'-'.$func.'.log';
        file_put_contents($logFile, $line, FILE_APPEND);
    }

    public function curl($url, $data, $method = 'POST', $dataType = 'json')
    {
        $curl = curl_init();

        if($dataType == 'json')
        {
            $headers[] = 'Content-Type: application/json;charset=utf-8';
            if(!empty($data)) $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            if($method == 'POST')  curl_setopt($curl, CURLOPT_POST, true);
            if($method == 'PUT')  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if($method == 'PATCH') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}