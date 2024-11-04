<?php
include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * Token login.
     *
     * @access public
     * @return void
     */
    public function tokenloginApi()
    {
        $this->app->loadConfig('cas');
        if ($_SERVER['QUERY_STRING'] != ''){
            $paramsArray = explode('&',$_SERVER['QUERY_STRING']);
            $params = [];
            foreach ($paramsArray as $item) {
                $arr = explode('=',$item);
                $params[$arr[0]] = $arr[1];
                ${$arr[0]} = $arr[1];
            }
        }

        $ticket = isset($ticket) ? $ticket : '';
        $module = isset($module) ? $module : '';
        $redirect = isset($redirecturl) ? $redirecturl : '';
        if (empty($ticket))
        {
            $msg = '缺少ticket参数！';
            $this->loadModel('mobileapi')->response('fail',$msg, array(),  0, 203,'tokenloginApi');
        }

        $ticket  = $ticket;
      //  $gotoUrl = $this->config->cas->authUrl . "?service=" . urlencode($this->config->cas->serviceUrl) . "&ticket=" . $ticket;
      //测试
//       $gotoUrl = $this->config->cas->authUrl . "?service=" . $this->config->cas->mobileServiceUrl."?module=workreport" . "&ticket=" . $ticket;
        //正式
        $gotoUrl = $this->config->cas->authUrl . "?service=" .$this->config->cas->mobileServiceUrl.'?redirecturl='. $redirect. "&ticket=" . $ticket;
        $output  = $this->curl($gotoUrl);

        $validateXML = simplexml_load_string($output, null, 0, 'cas', true);
        if($validateXML->authenticationFailure)
        {
            $msg = 'CAS接口请求认证失败！';
            $this->loadModel('mobileapi')->response('fail',$msg, array(),  0, 203,'tokenloginApi');
        }

        $res     = (array)$validateXML->authenticationSuccess;
        $account = isset($res['user']) ? $res['user'] : '';
        if(empty($account))
        {
            $msg = '从CAS接口中未查询到此用户！';
            $this->loadModel('mobileapi')->response('fail',$msg, array(),  0, 203,'tokenloginApi');
        }

        //使用ldap查询账号
        $user = $this->dao->select('*')->from(TABLE_USER)->where('ldap')->eq($account)->fetch();

        //$user = $this->loadModel('user')->identify($user->account, '1bf6717f338767ca7c67adfaa938ca77', null, true);
        $user = $this->loadModel('user')->identify($user->account, '', $user, true);
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
            $this->checkConfig($user->account);
            $this->checkBlock($user->account);
            $token =  $this->loadModel('mobileapi')->createToken($data);//生成token
            //$this->loadModel('mobileapi')->response('success', $this->lang->api->successful, $data = array('token' =>$token,'user' => $data,'test' =>$this->lang->api->serviceUrl."?token=".$token['access_token'].";".$token['refresh_token']),  0, 200);
           //测试
//            $this->locate($this->lang->api->h5Url.'#/home?module='.$module."&token=".$token['access_token'].";".$token['refresh_token']);
//           正式
            $this->locate(urldecode( $redirect)."&token=".$token['access_token'].";".$token['refresh_token']);
        }
        else
        {
            $msg =  '登录失败，系统中未查询到此用户！';
            $this->loadModel('mobileapi')->response('fail',$msg, array('user' => $account),  0, 203 ,'tokenloginApi');
        }
    }
   /**
     * 检查区块是否存在
     * @param $account
     */
    public function checkBlock($account){
        $initBlock = array(
            array('module' => 'my','title' => '我要报工', 'source' => 'workreport', 'block' => 'workreport', 'param' => '', 'order' => 0, 'grid' => 4, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '项目列表', 'source' => 'project', 'block' => 'project', 'param' => '{"orderBy":"id_desc","count":"15"}', 'order' => 9, 'grid' => 8, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '项目人力投入', 'source' => 'project', 'block' => 'projectteam', 'param' => '', 'order' => 8, 'grid' => 8, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '待处理', 'source' => '', 'block' => 'assigntome', 'param' => '{"todoNum":"20","taskNum":"20","bugNum":"20","riskNum":"20","issueNum":"20","storyNum":"20"}', 'order' => 7, 'grid' => 8, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '我近期参与的项目', 'source' => 'project', 'block' => 'recentproject', 'param' => '', 'order' => 6, 'grid' => 8, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '我的贡献', 'source' => '', 'block' => 'contribute', 'param' => '', 'order' => 5, 'grid' => 4, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '项目统计', 'source' => 'project', 'block' => 'statistic', 'param' => '{"count":"20"}', 'order' => 4, 'grid' => 8, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '我的待办', 'source' => 'todo', 'block' => 'list', 'param' => '{"count":"20"}', 'order' => 3, 'grid' => 4, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '最新动态', 'source' => '', 'block' => 'dynamic', 'param' => '', 'order' => 2, 'grid' => 4, 'height' => 0, 'hidden' => 0),
            array('module' => 'my','title' => '欢迎', 'source' => '', 'block' => 'welcome', 'param' => '', 'order' => 1, 'grid' => 8, 'height' => 0, 'hidden' => 0),
        );
        $work = $this->dao->select('*')->from(TABLE_BLOCK)
            ->where('account')->eq($account)->andWhere('module')->eq('my')->andWhere('source')->eq('workreport')->andWhere('(block ="workreport" or block ="list")')->fetchAll();
        if(!$work){
            foreach ($initBlock as $item) {
                $res = $this->dao->select('*')->from(TABLE_BLOCK)
                    ->where('account')->eq($account)->andWhere('module')->eq('my')->andWhere('source')->eq($item['source'])->andWhere('block')->eq($item['block'])->fetch();
                if(!$res){
                    $sql = "INSERT INTO zt_block( account, module, `type`, title, source, block, params, `order`, grid, height, hidden) VALUES('$account' , '$item[module]', '', '$item[title]', '$item[source]', '$item[block]', '$item[param]', $item[order], $item[grid], 0, 0)";
                    $this->dao->query($sql);
                }
            }
        }
    }

    /**
     * 新用户个性设置初始化
     * @param $account
     */
    public function checkConfig($account){
        $initConfig = array(
            array('module' => 'common','section' => '','key' => 'USER','value' => '2'),
            array('module' => 'common','section' => '','key' => 'programLink','value' => 'program-browse'),
            array('module' => 'common','section' => '','key' => 'productLink','value' => 'product-all'),
            array('module' => 'common','section' => '','key' => 'projectLink','value' => 'project-browse'),
            array('module' => 'common','section' => '','key' => 'secondLink','value' => 'outwarddelivery-browse'),
            array('module' => 'common','section' => '','key' => 'preferenceSetted','value' => '1'),
            array('module' => 'my','section' => 'common','key' => 'blockInited','value' => '1'),
            array('module' => 'my','section' => 'block','key' => 'initVersion','value' => '2'),
        );
        $res = $this->dao->select('*')->from(TABLE_CONFIG)
            ->where('owner')->eq($account)->andWhere('module')->eq('common')->andWhere('`key`')->eq('preferenceSetted')->fetch();
        if(!$res){
            foreach ($initConfig as $item) {
                $sql = "INSERT INTO zt_config( owner, `module`, `section` , `key`, `value`) VALUES('$account' , '$item[module]','$item[section]','$item[key]','$item[value]')";
                $this->dao->query($sql);
            }
        }
    }
    /**
     * Curl.
     *
     * @param  string $url
     * @param  mixed  $params
     * @param  string $method
     * @param  mixed  $header
     * @access public
     * @return void
     */
    public function curl($url = '', $params = [], $method = 'GET', $header = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if(strtoupper($method) == 'POST')
        {
            if(!empty($params))
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }
        if(!empty($header))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


}

