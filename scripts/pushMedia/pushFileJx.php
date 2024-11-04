<?php

class pushFileJx extends DBbase
{
    const LOCAL_DIR        = '/ftpdatas';                          //文件服务器目录
    const REMOTE_DIR       = "";                               //清总服务器目录
    public function doPushFileJx($localFile, $url, $params=array())
    {
        $resultObj = array();
        //如果没有/ftpdatas开头，就自动拼接
        if(!(strpos($localFile,'/ftpdatas') === 0)) {
            $localFile = '/ftpdatas/'.$localFile;
        }

        $localRealFile = $localFile;

        $fileInfoArr = explode('/', $localRealFile);
        $zipName = end($fileInfoArr);


        if (!file_exists($localRealFile) || empty($localFile)) {
            $resultObj['status'] = 'fail';
            $resultObj['msg'] = 'noFile';
            return $resultObj; //'文件不存在'
        } //检查下载文件是否存在

        $md5value = $this->checkLocalMd5_sc($localRealFile);
        if($md5value === 0 or $md5value < 0) {
            $resultObj['status'] = 'fail';
            $resultObj['msg'] = 'md5Err';
            return $resultObj;
        }
        $headers = array();
        $pushAppId = 'CFIT';
        $pushAppSecret = '063dfb7fc52b7a3a3199476f5e238eed';
        $headers[] = 'appId: ' . $pushAppId;
        $headers[] = 'appSecret: ' . $pushAppSecret;
        $ts = time();
        $headers[] = 'ts: ' . $ts;
        $uuid = $this->create_guid();
        $headers[] = 'nonce: ' . $uuid;
        $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
        $headers[] = 'sign: ' . $sign;
        $headers[] ='Content-Type: multipart/form-data';
        $params['type'] = $params['type'];
        $params['md5'] = $md5value;
        $params['file'] = new \CURLFile($localRealFile,'',$zipName);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");//3.请求方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        $response = curl_exec($curl);
        $responseObj = json_decode($response);
        if($responseObj->status == 'success'){
            $fileId = ','.$responseObj->data;
            $resultObj['status'] = 'success';
            $resultObj['msg'] = 'success';
            $resultObj['fileId'] = $fileId;
            return $resultObj; //成功
        }
        $resultObj['status'] = 'fail';
        $resultObj['msg'] = $responseObj->error;
        return $resultObj;
    }

    public function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'] ?? time();
        $data .= $_SERVER['HTTP_USER_AGENT'] ?? '';
        $data .= $_SERVER['LOCAL_ADDR'] ?? '';
        $data .= $_SERVER['LOCAL_PORT'] ?? '80';
        $data .= $_SERVER['REMOTE_ADDR'] ?? '';
        $data .= $_SERVER['REMOTE_PORT'] ?? '80';
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);
        return $guid;
    }

    /**
     * 检查本地md5文件是否正确
     * @param $localFile
     */
    public function checkLocalMd5_sc($localFile)
    {
        $localFileMd5   = $this->getMd5FileName_sc($localFile);            ///aaa.zip 转成 aaa.md5
        $localRealFileMd5   = $localFileMd5;          //本地文件地址MD5
        if (!file_exists($localRealFileMd5)) {
            $localFileMd5   = $this->getMd5OrgName_sc($localFile);         ///aaa.zip 转成 aaa.org
            $localRealFileMd5   = $localFileMd5;      //本地临时文件地址MD5
            if (!file_exists($localRealFileMd5)) {
                $localFileMd5   = $this->getMd5OrgFile($localFile);     //将文件名转成md5
                $localRealFileMd5 = $localFileMd5;
                if(!file_exists($localRealFileMd5)){
                    return 0;                                               //'md5文件不存在';
                }
            }
        }
        if (!file_exists($localRealFileMd5)) {
            return self::$_statusList['md5Err']; //'md5文件不存在'
        } //检查下载文件是否存在

        $md5value = $this->getFileMd5_sc($localRealFileMd5);                   //文件里的md5值
        $myMd5value = md5_file($localFile);   //现算的md5值
        if($md5value != $myMd5value) {
            return 0;                                                   //md5 不对
        }
        return $md5value;      //                                          //没问题返回md5值
    }
    //把后缀变成MD5
    public function getMd5FileName_sc($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把后缀变成org
    public function getMd5OrgName_sc($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'org';
    }

    //把文件名变成md5.org
    public function getMd5OrgFile($filename)
    {
        $arr = explode('/', $filename);
        $arr[sizeof($arr)-1]='md5.org';
        return rtrim(implode('/',$arr),'/');
    }

    //读MD5文件中的MD5值
    public function getFileMd5_sc($filename)
    {
        $info = file_get_contents($filename);
        $md5 = substr($info, 0,32);
        return $md5;
    }
}
