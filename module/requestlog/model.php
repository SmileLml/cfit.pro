<?php
class requestlogModel extends model
{
    /**
     * Obtain request log information by id.
     * 通过id获取请求日志信息。
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function getByID($id)
    {
        return $this->dao->select('*')->from(TABLE_REQUESTLOG)->where('id')->eq($id)->fetch();
    }

    /**
     * Configuration parameters required to handle paging.
     * 处理分页所需的配置参数。
     *
     * @param  string $actionURL
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getLogList($browseType = 'all', $queryID, $orderBy, $pager)
    {
        /* Get the query criteria from session. */
        /* 从session中获取查询条件。*/
        $requestlogQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('requestlogQuery', $query->sql);
                $this->session->set('requestlogForm', $query->form);
            }
            if($this->session->requestlogQuery == false) $this->session->set('requestlogQuery', ' 1=1');
            $requestlogQuery = $this->session->requestlogQuery;
        }

        $result = $this->dao->select('*')->from(TABLE_REQUESTLOG)
            ->where('id')->gt(0)
            ->beginIF($browseType == 'bysearch')->andWhere($requestlogQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        return $result;
    }

    /**
     * Save the request log.
     * 保存请求日志。
     *
     * @param  string $url
     * @param  string $objectType
     * @param  string $purpose
     * @param  string $requestType
     * @param  string $params
     * @param  string $response
     * @param  string $status
     * @param  string $extra
     * @access public
     * @return void
     */
    public function insideSaveRequestLog($objectType, $purpose, $logID = 0, $result = array())
    {
        /* When used to re-request, update the old data result. */
        /* 用于重新请求时，更新旧数据结果。 */
        if(!empty($logID))
        {
            $data = new stdClass();
            $data->response = json_encode($result);
            $data->status   = $result['result'];
            $this->dao->update(TABLE_CRMLOG)->data($data)->where('id')->eq($logID)->exec();
            return true;
        }

        $requestType = $_SERVER['REQUEST_METHOD'];
        $params      = array();
        if($requestType == 'POST') $params = $_POST;

        $log              = new stdClass();
        $log->url         = $this->app->getURI(true);
        $log->objectType  = $objectType;
        $log->purpose     = $purpose;
        $log->requestType = $requestType;
        $log->status      = 'fail';
        $log->params      = json_encode($params);
        $log->response    = '';
        $log->requestDate = helper::now();
        $this->dao->insert(TABLE_REQUESTLOG)->data($log)->exec();

        return $this->dao->lastInsertId();
    }

    /**
     * Save the request log.
     * 保存请求日志。
     *
     * @param  string $url
     * @param  string $objectType
     * @param  string $purpose
     * @param  string $requestType
     * @param  string $params
     * @param  string $response
     * @param  string $status
     * @param  string $extra
     * @access public
     * @return void
     */
    public function saveRequestLog($url, $objectType = '', $purpose = '', $requestType = '', $params = array(), $response = '', $status = '', $extra = '', $objectId = 0)
    {
        $now              = helper::now();
        $log              = new stdClass();
        $log->url         = $url;
        $log->objectType  = $objectType;
        $log->purpose     = $purpose;
        $log->requestType = $requestType;
        $log->status      = $status;
        $log->params      = json_encode($params);
        $log->response    = $response;
        $log->requestDate = $now;
        $log->extra       = $extra;
        $log->objectId    = $objectId;

        $this->dao->insert(TABLE_REQUESTLOG)->data($log)->exec();
        $id = $this->dao->lastInsertId();
        //请求失败发生邮件提醒
        if($status == 'fail'){
             $this->sendmail($id);
        }
        return $id;
    }

    public function updateRequestLog($logID = 0, $result = array())
    {
        if(!empty($logID))
        {
            $data = new stdClass();
            $data->status   = $result['result'];
            $data->response = json_encode($result);
            $this->dao->update(TABLE_REQUESTLOG)->data($data)->where('id')->eq($logID)->exec();
            return true;
        }
    }

    /**
     * Configuration parameters required to handle paging.
     * 处理分页所需的配置参数。
     *
     * @param  string $actionURL
     * @param  int    $queryID
     * @access public
     * @return void
     */
    public function buildSearchForm($actionURL, $queryID)
    {
        $this->config->requestlog->search['actionURL'] = $actionURL;
        $this->config->requestlog->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->requestlog->search);
    }

    /* 返回响应信息。*/
    public function response($result = 'fail', $message = '', $data = array(), $logID = 0, $code = 0)
    {
        $response = array('result' => $result, 'message' => $message, 'data' => $data,  'code' => $code);
        $this->updateRequestLog($logID, $response);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        //请求失败发送邮件提醒
        if($response['result'] == 'fail' && !empty($logID)){
           $this->sendmail($logID);
        }
        die();
    }

    /* 判断请求方式和参数是否合规。*/
    public function judgeRequestMode($logID = 0)
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->response('fail', $this->lang->requestlog->notPostRequest, array(), $logID);
        if(empty($_POST)) $this->response('fail', $this->lang->requestlog->parameterEmpty, array(), $logID);
    }

    /* 删除旧的对象附件数据。*/
    public function deleteOldFile($objectType, $objectID)
    {
        $files = $this->dao->select('*')->from(TABLE_FILE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->fetchAll();

        foreach($files as $file)
        {
            $file = $this->loadModel('file')->getById($file->id);
            $this->dao->delete()->from(TABLE_FILE)->where('id')->eq($file->id)->exec();
            if(file_exists($file->realPath)) @unlink($file->realPath);
        }
    }

    /* 为指定对象下载url地址中的文件。*/
    public function downloadApiFile($url, $fileTitle, $objectType, $objectID)
    {
        $extension = $this->getFileExtension($url);
        $time     = date('Ym/', time());
        $fileName = md5($url . time());
        $pathName = $time . $fileName . '.' . $extension;

        $now        = helper::today();
        $insertFile = array();

        $insertFile['pathname']  = $pathName;
        $insertFile['title']     = $fileTitle;
        $insertFile['extension'] = $extension;
        $insertFile['size']      = 0;

        $insertFile['objectType'] = $objectType;
        $insertFile['objectID']   = $objectID;
        $insertFile['addedBy']    = $this->app->user->account;
        $insertFile['addedDate']  = $now;
        $insertFile['downloads']  = 0;
        $insertFile['extra']      = '';
        $insertFile['deleted']    = 0;
        $this->dao->insert(TABLE_FILE)->data($insertFile)->exec();
        $fileID = $this->dao->lastInsertID();

        $savePath   = $this->getSavePath();
        $uploadPath = $savePath . $time . $fileName;
        $this->downloadFile($url, $uploadPath);

        $size = filesize($uploadPath);
        $this->dao->update(TABLE_FILE)->set('size')->eq($size)->where('id')->eq($fileID)->exec();
    }
    public function downloadApiFileByHttps($url, $fileTitle, $objectType, $objectID)
    {
        /*$extension = $this->getFileExtension($url);*/
        $extension = substr($fileTitle, strrpos($fileTitle, '.') + 1);
        $time     = date('Ym/', time());
        $fileName = md5($url . time());
        $pathName = $time . $fileName . '.' . $extension;

        $now        = helper::today();
        $insertFile = array();

        $insertFile['pathname']  = $pathName;
        $insertFile['title']     = $fileTitle;
        $insertFile['extension'] = $extension;
        $insertFile['size']      = 0;

        $insertFile['objectType'] = $objectType;
        $insertFile['objectID']   = $objectID;
        $insertFile['addedBy']    = $this->app->user->account;
        $insertFile['addedDate']  = $now;
        $insertFile['downloads']  = 0;
        $insertFile['extra']      = '';
        $insertFile['deleted']    = 0;
        $this->dao->insert(TABLE_FILE)->data($insertFile)->exec();
        $fileID = $this->dao->lastInsertID();

        $savePath   = $this->getSavePath();
        $uploadPath = $savePath . $time . $fileName;
        $this->downloadFileByHttps($url, $uploadPath);

        $size = filesize($uploadPath);
        $this->dao->update(TABLE_FILE)->set('size')->eq($size)->where('id')->eq($fileID)->exec();
    }

    /* 通过url下载文件存放指定目录下。*/
    public function downloadFile($url, $path)
    {
        $newfname = $path;

        // 使用http1.1协议下载
        $stream = stream_context_create(array('http' => array("protocol_version" => 1.1)));
        $file   = fopen($url, 'rb', false, $stream);

        if($file)
        {
            $newf = fopen($newfname, 'wb');
            if ($newf)
            {
                while(!feof($file))
                {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }

        if($file) fclose($file);
        if($newf) fclose($newf);
    }
    public function downloadFileByHttps($url, $path)
    {
        $newfname = $path;

        $stream = stream_context_create(//兼容HTTPS
            [
                'http' => ['protocol_version' => 1.1,'timeout' => 30],
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
            ]
        );
        $file   = fopen($url, 'rb', false, $stream);

        if($file)
        {
            $newf = fopen($newfname, 'wb');
            if ($newf)
            {
                while(!feof($file))
                {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }

        if($file) fclose($file);
        if($newf) fclose($newf);
    }

    /* 获取文件要保存的路径。*/
    public function getSavePath()
    {
        $now      = time();
        $savePath = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . date('Ym/', $now);
        if(!file_exists($savePath))
        {
            @mkdir($savePath, 0777, true);
            touch($savePath . 'index.html');
        }

        return dirname($savePath) . '/';
    }

    /* 获取链接中文件的扩展名。*/
    public function getFileExtension($url)
    {
        $extension = '';
        $data = parse_url($url);
        if(isset($data['path']))
        {
            $file = pathinfo($data['path']);
            if(isset($file['extension'])) $extension = $file['extension'];
        }
        return $extension;
    }

    /**
     * Http.
     *
     * @param  string       $url
     * @param  string|array $data
     * @param  array        $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array        $headers   Set request headers.
     * @param  string       $mehtod POST|PATCH
     * @static
     * @access public
     * @return string
     */
    public function http($url, $data = array(), $method = 'POST', $dataType = 'data', $options = array(), $headers = array())
    {
        global $lang, $app;
        if(!extension_loaded('curl')) $this->response('fail', $lang->error->noCurlExt);

        if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . zget($_SERVER, 'REMOTE_ADDR', '');
        if($dataType == 'json')
        {
            $headers[] = 'Content-Type: application/json;charset=utf-8';
            if(!empty($data)) $data = json_encode($data);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FAILONERROR, FALSE);

        if(!empty($data))
        {
            if($method == 'POST')  curl_setopt($curl, CURLOPT_POST, true);
            if($method == 'PUT')  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if($method == 'PATCH') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);//获取状态码
        if($httpCode != 200){
            $headerSize = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
            $header = substr($response,0,$headerSize);
            $response = json_encode(array(
                'httpcode' => $httpCode,
                'error' => curl_error($curl),
                'header' => $header
            ));
        }
        curl_close($curl);

        return $response;
    }

    /**
     * CURLOPT_FAILONERROR 参数设置为忽略错误.
     *
     * @param  string       $url
     * @param  string|array $data
     * @param  array        $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array        $headers   Set request headers.
     * @param  string       $mehtod POST|PATCH
     * @static
     * @access public
     * @return string
     */
    public function newHttp($url, $data = array(), $method = 'POST', $dataType = 'data', $options = array(), $headers = array())
    {
        global $lang, $app;
        if(!extension_loaded('curl')) $this->response('fail', $lang->error->noCurlExt);

        if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . zget($_SERVER, 'REMOTE_ADDR', '');
        if($dataType == 'json')
        {
            $headers[] = 'Content-Type: application/json;charset=utf-8';
            if(!empty($data)) $data = json_encode($data);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);

        if(!empty($data))
        {
            if($method == 'POST')  curl_setopt($curl, CURLOPT_POST, true);
            if($method == 'PUT')  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if($method == 'PATCH') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

   /**
     * Send mail.
     *
     * @access public
     * @return void
     */
    public function sendmail($logID)
    {
        $this->loadModel('mail');
        $to = $this->lang->requestlog->userList['user'];
        $log = $this->getByID($logID);
        /* 获取后台通知中设置的邮件发信*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setRequestFailLogMail) ? $this->config->global->setRequestFailLogMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'requestlog');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);
        $toList = $to;
        $ccList = '';
        list($toList, $ccList) = array($toList,$ccList);

        /* 处理邮件标题*/
        $subject = $mailTitle;
        /* Send emails. */
        if($toList){
            $this->mail->send($toList, $subject, $mailContent, $ccList);
        }
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));

    }
}
