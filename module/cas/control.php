<?php

class cas extends control
{

    /**
     * Set.
     *
     * @access public
     * @return void
     */
    public function set()
    {
        if($_POST)
        {
            $settings = fixer::input('post')->get();
            foreach(explode(',', $this->config->cas->set->requiredFields) as $requiredField)
            {
                if(empty($settings->$requiredField)) die(js::alert(sprintf($this->lang->cas->error->noempty, $this->lang->cas->$requiredField)));
            }

            $this->loadModel('setting');
            $this->setting->deleteItems('owner=system&module=cas');
            $this->setting->setItems('system.cas', $settings);

            if(dao::isError()) die(js::error(dao::getError()));
            echo js::alert($this->lang->cas->successSave);
            die(js::reload('parent'));
        }

        $this->view->title      = $this->lang->cas->common;
        $this->view->casConfig = empty($this->app->config->cas) ? '' : $this->app->config->cas;
        $this->display();
    }

    /**
     * Login.
     *
     * @param  string $referer
     * @access public
     * @return void
     */
    public function login($referer = '')
    {
        $gotoUrl = $this->config->cas->loginUrl . "?service=" . urlencode($this->config->cas->serviceUrl);
        $this->locate($gotoUrl);
    }

    /**
     * Token login.
     *
     * @access public
     * @return void
     */
    public function tokenlogin()
    {
        if (empty($_GET['ticket']))
        {
            echo '缺少ticket参数！';
            return false;
        }

        $ticket  = $_GET['ticket'];
        $gotoUrl = $this->config->cas->authUrl . "?service=" . urlencode($this->config->cas->serviceUrl) . "&ticket=" . $ticket;
        $output  = $this->curl($gotoUrl);

        $validateXML = simplexml_load_string($output, null, 0, 'cas', true);
        if($validateXML->authenticationFailure)
        {
            echo 'CAS接口请求认证失败！';
            return false;
        }

        $res     = (array)$validateXML->authenticationSuccess;
        $account = isset($res['user']) ? $res['user'] : '';
        if(empty($account))
        {
            echo '从CAS接口中未查询到此用户！';
            return false;
        }

        //使用ldap查询账号
        $user = $this->dao->select('*')->from(TABLE_USER)->where('ldap')->eq($account)->fetch();
        //$user = $this->loadModel('user')->identify($user->account, '1bf6717f338767ca7c67adfaa938ca77', null, true);
        $_POST['keepLogin'] = 'on';
        $user = $this->loadModel('user')->identify($user->account, '', $user, true);
        if($user)
        {
            $ip   = $this->server->remote_addr;
            $last = $this->server->request_time;

            $user->lastTime = $user->last;
            $user->last     = date(DT_DATETIME1, $last);
            $user->admin    = strpos($this->app->company->admins, ",{$user->account},") !== false;

            $this->loadModel('user')->login($user);

            if($referer) $referer = helper::safe64Decode($referer);
            if($referer) die(js::locate($referer, 'parent'));
            die(js::locate($this->createLink($this->config->default->module), 'parent'));
        }
        else
        {
           /* echo '登录失败，系统中未查询到此用户！';
            return false;*/
            //20240614  更新如未查询到用户，自动登出cas,并返回cas登录页
            //die( "<script> window.setTimeout(function(){alert('登录失败，系统中未查询到此用户！')},300);location.href='".inLink('logout')."'</script>");
            die( "<script> alert('登录失败，系统中未查询到此用户！');location.href='".inLink('logout')."'</script>");
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

    /**
     * Logout.
     *
     * @access public
     * @return void
     */
    public function logout()
    {
        if(isset($this->app->user->id)) $this->loadModel('action')->create('user', $this->app->user->id, 'logout');
        session_destroy();
        setcookie('za', false);
        setcookie('zp', false);

        $loginUrl  = urlencode($this->config->cas->loginUrl);
        $locateUrl = $this->config->cas->loginOut . "?service={$loginUrl}?service=" . urlencode($this->config->cas->serviceUrl);
        $this->locate($locateUrl);
    }

}
