<?php
/**
 * The model file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php
class myModel extends model
{
    /**
     * Set menu.
     * 
     * @access public
     * @return void
     */
    public function setMenu()
    {
        /* Adjust the menu order according to the user role. */
        $flowModule = $this->config->global->flow . '_my';
        $customMenu = isset($this->config->customMenu->$flowModule) ? $this->config->customMenu->$flowModule : array();

        if(empty($customMenu))
        {
            $role = $this->app->user->role;
            if($role == 'qa')
            {
                $taskOrder = '15';
                $bugOrder  = '20';

                unset($this->lang->my->menuOrder[$taskOrder]);
                $this->lang->my->menuOrder[32] = 'task';
                $this->lang->my->dividerMenu = str_replace(',task,', ',' . $this->lang->my->menuOrder[$bugOrder] . ',', $this->lang->my->dividerMenu);
            }
            elseif($role == 'po')
            {
                $requirementOrder = 29;
                unset($this->lang->my->menuOrder[$requirementOrder]);

                $this->lang->my->menuOrder[15] = 'story';
                $this->lang->my->menuOrder[16] = 'requirement';
                $this->lang->my->menuOrder[30] = 'task';
                $this->lang->my->dividerMenu = str_replace(',task,', ',story,', $this->lang->my->dividerMenu);
            }
            elseif($role == 'pm')
            {
                $projectOrder = 35;
                unset($this->lang->my->menuOrder[$projectOrder]);

                $this->lang->my->menuOrder[17] = 'myProject';
            }
        }
    }

    /**
     * 获取帮助手册所有涉及菜单模块
     */
    public function getDocTypes(){
        $navs = $this->loadModel('custom')->getItems('lang=zh-cn&module=helpdoc&section=navOrderList');
        array_multisort(array_column($navs,'order'), SORT_ASC, $navs);
        $keysOrder = array_column($navs,'key');
        $data = $this->dao->select("*")->from(TABLE_FLOW_HANDBOOK)->where('deleted')->eq(0)->orderBy('ChapterNumber')->fetchall();
        //取出所有存在手册的模块
        $modules = array_values(array_unique(explode(',',implode(',',array_column($data,'type')))));
        $filed = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('handbook')->andWhere('field')->eq('type')->fetch();
        $options = json_decode($filed->options,true);

        $navList = [];
        foreach ($keysOrder as $key=>$val){
            if (isset($options[$val]) && $options[$val] != ''){
                $navList[$val] = $options[$val];
            }
        }
        return ['options'=>$navList,'data'=>$data];
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送授权邮件
     * shixuyang
     */
    public function sendauthorizationmail($authorization)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setAuthorizationMail) ? $this->config->global->setAuthorizationMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'authorization';
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'my');
        $viewFile   = $modulePath . 'view/sendauthorizationmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendauthorizationmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendauthorizationmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendauthorizationmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 处理邮件标题。*/
        $subject = $mailTitle;
        $toList = $authorization->authorizedPerson;
        $ccList = '';

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送取消授权邮件
     * shixuyang
     */
    public function sendcancelmail($authorization)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setAuthorizationMail) ? $this->config->global->setAuthorizationMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'authorization';
        $mailTitle = '【通知】授权取消提醒';

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'my');
        $viewFile   = $modulePath . 'view/sendcancelmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendcancelmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendcancelmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendcancelmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 处理邮件标题。*/
        $subject = $mailTitle;
        $toList = $authorization->authorizedPerson;
        $ccList = '';

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送即将超期邮件
     * shixuyang
     */
    public function sendRemindMail($authorization)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setAuthorizationMail) ? $this->config->global->setAuthorizationMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'authorization';
        $mailTitle = '【通知】授权期限将至提醒';

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'my');
        $viewFile   = $modulePath . 'view/sendremindmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendremindmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendremindmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendremindmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 处理邮件标题。*/
        $subject = $mailTitle;
        $toList = $authorization->authorizedPerson.','.$authorization->authorizer;
        $ccList = '';

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送即将超期邮件
     * shixuyang
     */
    public function sendToRemindMail(){
        $enddate = date('Y-m-d', strtotime('+1 day'));
        $startdate = date('Y-m-d');
        $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('permanently')->eq('1')->andWhere('endTime')->le($enddate)->andWhere('endTime')->ge($startdate)->andWhere('enabled')->eq(2)->andWhere('deleted')->ne('2')->fetchAll();
        foreach ($authorizationList as $authorization){
            $startDate = new DateTime($authorization->startTime);
            $endDate = new DateTime($authorization->endTime);
            $nowDate = new DateTime($startdate);
            echo $nowDate;
            echo $endDate->diff($nowDate)->days;
            if($startDate->diff($endDate)->days >= 3 and $endDate->diff($nowDate)->days == 1){
                $authorization->authorizerTerm = date('Y-m-d', strtotime($authorization->startTime)).'~'.date('Y-m-d', strtotime($authorization->endTime));
                $authorization->context = $this->lang->my->remindcontext;
                echo $authorization->authorizedPerson;
                $this->sendRemindMail($authorization);
            }
        }
    }

    // 获取项目结项列表
    public function getUserClosingItemCount($orderBy)
    {
        $account     = $this->app->user->account;
        $dealuserQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealuser))';
        $ret = $this->dao->select('*')->from(TABLE_CLOSINGITEM)
            ->where($dealuserQuery)
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll();
        return empty($ret) ? 0 : count($ret);
    }

// 获取项目结项意见列表
    public function getUserClosingAdviseCount($orderBy)
    {
        $account     = $this->app->user->account;
        $dealuserQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealuser))';
        $ret = $this->dao->select('*')->from(TABLE_CLOSINGADVISE)
            ->where($dealuserQuery)
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll();
        return empty($ret) ? 0 : count($ret);
    }

// 授权保存
    public function authorization()
    {
        $comment = '';
        $authorizationList = fixer::input('post')
            ->get();
        $oldauthorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($authorizationList->authorizerAccount)->andWhere('deleted')->ne('2')->orderBy('num')->fetchAll('id');
        $oldMaxauthorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($authorizationList->authorizerAccount)->orderBy('num')->fetchAll('id');
        $maxnum = 0;
        foreach ($oldMaxauthorizationList as $oldData){
            if($oldData->num > $maxnum){
                $maxnum = $oldData->num;
            }
        }

        $dataList = array();
        $sendMailList = array();
        $sendCancelMailList = array();
        $users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        //拼接数据
        foreach ($authorizationList->code as $key=>$value){
            $data = new stdClass();
            if(!empty($authorizationList->id[$key])){
                $errorNum = $authorizationList->id[$key];
            }else{
                $errorNum = $key;
            }

            $data->objectType = implode("," , $authorizationList->objectType[$key]);
            $data->authorizer = $authorizationList->authorizerAccount;
            $data->authorizedPerson = implode(',',$authorizationList->authorizedPerson[$key]);
            if(!empty($authorizationList->permanently[$key])){
                $data->permanently = '2';
                $data->startTime = '';
                $data->endTime = '';
            }else{
                $data->permanently = '1';
                $data->startTime = $authorizationList->startTime[$key];
                $data->endTime = $authorizationList->endTime[$key];
                if(!empty($authorizationList->enabled[$key])){
                    if(empty($data->startTime) or empty($data->endTime)){
                        return dao::$errors[''] = sprintf($this->lang->my->timeNullError, $errorNum);
                    }
                    if(date('Y-m-d', strtotime($data->startTime)) != $data->startTime || date('Y-m-d', strtotime($data->endTime)) != $data->endTime){
                        return dao::$errors[''] = sprintf($this->lang->my->timeError, $errorNum);
                    }
                }
            }

            if(!empty($authorizationList->enabled[$key])){
                if($data->permanently == '1' and !empty($data->authorizedPerson)){
                    if(empty($data->startTime) or empty($data->endTime)){
                        return dao::$errors[''] = sprintf($this->lang->my->timeNullError, $errorNum);
                    }
                    if(strtotime($data->startTime) <= strtotime(helper::now())){
                        return dao::$errors[''] = sprintf($this->lang->my->startTimeError, $errorNum);
                    }
                    if(strtotime($data->endTime) < strtotime($data->startTime)){
                        return dao::$errors[''] = sprintf($this->lang->my->endTimeError, $errorNum);
                    }
                }
                if(empty($data->objectType)){
                    return dao::$errors[''] = sprintf($this->lang->my->objectTypeNullError, $errorNum);
                }
                if(empty($data->authorizedPerson)){
                    return dao::$errors[''] = sprintf($this->lang->my->authorizerNullError, $errorNum);
                }
                $data->enabled = '2';
            }else{
                $data->enabled = '1';
            }
            $oldData = null;
            if(!empty($authorizationList->id[$key])){
                $data->id = $authorizationList->id[$key];
                $oldData = $oldauthorizationList[$authorizationList->id[$key]];
                unset($oldauthorizationList[$authorizationList->id[$key]]);
                $data->num = $key;
            }else{
                $maxnum = $maxnum+1;
                $data->num = $maxnum;
            }
            //判断是创建数据还是编辑数据
            $data->updated = 'no';
            if(!empty($oldData)){
                $data->updated = 'yes';
                $oldData->startTime = $oldData->startTime == '0000-00-00 00:00:00'?'':date('Y-m-d',strtotime($oldData->startTime));
                $oldData->endTime = $oldData->endTime == '0000-00-00 00:00:00'?'':date('Y-m-d',strtotime($oldData->endTime));
            }


            if(empty($oldData)){
                if($data->enabled == '2'){
                    array_push($sendMailList, $data);
                }
            }else if($oldData->enabled == '1' && $data->enabled == '2'){
                array_push($sendMailList, $data);
                $comment .= $this->diffData($oldData, $data, $users).'<br>';
            }else if($data->enabled == '2'&& ($oldData->authorizedPerson != $data->authorizedPerson || $oldData->startTime != $data->startTime ||
                    $oldData->endTime != $data->endTime || $oldData->permanently != $data->permanently || $oldData->objectType != $data->objectType)){
                array_push($sendMailList, $data);
                $comment .= $this->diffData($oldData, $data, $users).'<br>';
            }else if($oldData->enabled == '2' && $data->enabled == '1'){
                array_push($sendCancelMailList, $data);
                $comment .= $this->diffData($oldData, $data, $users).'<br>';
            }else if($oldData->authorizedPerson != $data->authorizedPerson || $oldData->startTime != $data->startTime ||
                $oldData->endTime != $data->endTime || $oldData->permanently != $data->permanently || $oldData->objectType != $data->objectType){
                $comment .= $this->diffData($oldData, $data, $users).'<br>';
            }
            array_push($dataList, $data);
        }
        foreach ($oldauthorizationList as $oldData){
            $comment .= $this->deldiffData($oldData, $users).'<br>';
        }

        //插入数据
        $this->dao->begin();  //开启事务
        foreach ($oldauthorizationList as $oldData){
            $this->dao->update(TABLE_AUTHORIZATION)
                ->set('deleted')->eq('2')
                ->where('id')->eq($oldData->id)
                ->exec();
            array_push($sendCancelMailList, $oldData);
        }
        foreach ($dataList as $data){
            if($data->updated == 'yes'){
                $this->dao->update(TABLE_AUTHORIZATION)
                    ->set('authorizedPerson')->eq($data->authorizedPerson)
                    ->set('startTime')->eq($data->startTime)
                    ->set('endTime')->eq($data->endTime)
                    ->set('permanently')->eq($data->permanently)
                    ->set('updatetime')->eq(helper::now())
                    ->set('enabled')->eq($data->enabled)
                    ->set('objectType')->eq($data->objectType)
                    ->where('id')->eq($data->id)
                    ->exec();
            }else{
                unset($data->updated);
                $data->createtime = helper::now();
                $this->dao->insert(TABLE_AUTHORIZATION)->data($data)->exec();
                $data->id = $this->dao->lastInsertId();
                $comment .= $this->newdiffData($data, $users).'<br>';
            }
        }
        if (dao::isError()) {
            $this->dao->rollBack();
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            die(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $this->dao->commit(); //提交事务
        //组装邮件
        foreach ($sendMailList as $data){
            if($data->permanently == '2'){
                $data->authorizerTerm = $this->lang->my->permanently;
            }else{
                $data->authorizerTerm = date('Y-m-d', strtotime($data->startTime)).'~'.date('Y-m-d', strtotime($data->endTime));
            }
            $this->sendauthorizationmail($data);
            if($data->updated == 'yes'){
                foreach ($oldauthorizationList as $oldauthorization){
                    if($oldauthorization->objectType == $data->objectType){
                        $oldUser = explode(',', $oldauthorization->authorizedPerson);
                        $newUser = explode(',', $data->authorizedPerson);
                        $diff = array_diff($oldUser, $newUser);
                        $data->authorizedPerson = implode(',', $diff);
                        $data->context = sprintf($this->lang->my->cancelcontext, helper::now());
                        $this->sendcancelmail($data);
                    }
                }
            }
        }
        foreach ($sendCancelMailList as $data){
            $data->context = sprintf($this->lang->my->cancelcontext, helper::now());
            $this->sendcancelmail($data);
        }
        return $comment;
    }

    public function diffData($oldData, $newData, $users){
        $diffStr  = $this->lang->my->num."：".$newData->id."，".$this->lang->my->authorizer."：".zget($users, $newData->authorizer);
        if($newData->objectType != $oldData->objectType){
            $diffStr = $diffStr."，".$this->lang->my->objectType."：由“".zmget($this->lang->my->objectTypeList, $oldData->objectType)."”修改为“".zmget($this->lang->my->objectTypeList, $newData->objectType)."”";
        }
        if($newData->authorizedPerson != $oldData->authorizedPerson){
            $diffStr = $diffStr."，".$this->lang->my->authorizedPerson."：由“".zmget($users, $oldData->authorizedPerson)."”修改为“".zmget($users, $newData->authorizedPerson)."”";
        }
        if($newData->startTime != $oldData->startTime){
            $diffStr = $diffStr."，".$this->lang->my->startTime."：由“".$oldData->startTime."”修改为“".$newData->startTime."”";
        }
        if($newData->endTime != $oldData->endTime){
            $diffStr = $diffStr."，".$this->lang->my->endTime."：由“".$oldData->endTime."”修改为“".$newData->endTime."“";
        }
        if($newData->permanently != $oldData->permanently){
            $diffStr = $diffStr."，".$this->lang->my->permanently."：由“".$this->lang->my->permanentlyList[$oldData->permanently]."”修改为“".$this->lang->my->permanentlyList[$newData->permanently]."“";
        }
        if($newData->enabled != $oldData->enabled){
            $diffStr = $diffStr."，".$this->lang->my->enabled."：由“".$this->lang->my->permanentlyList[$oldData->enabled]."”修改为“".$this->lang->my->permanentlyList[$newData->enabled]."“";
        }
        return $diffStr;
    }

    public function newdiffData($newData, $users){
        $diffStr  = $this->lang->my->num."：".$newData->id."新增，".$this->lang->my->authorizer."：".zget($users, $newData->authorizer)."，".$this->lang->my->objectType."：由“”修改为“".zmget($this->lang->my->objectTypeList, $newData->objectType)."”";
        $diffStr = $diffStr."，".$this->lang->my->authorizedPerson."：由“”修改为“".zmget($users, $newData->authorizedPerson)."”";
        $diffStr = $diffStr."，".$this->lang->my->startTime."：由“”修改为“".$newData->startTime."”";
        $diffStr = $diffStr."，".$this->lang->my->endTime."：由“”修改为“".$newData->endTime."”";
        $diffStr = $diffStr."，".$this->lang->my->permanently."：由“”修改为“".$this->lang->my->permanentlyList[$newData->permanently]."”";
        $diffStr = $diffStr."，".$this->lang->my->enabled."：由“”修改为“".$this->lang->my->permanentlyList[$newData->enabled]."”";
        return $diffStr;
    }

    public function deldiffData($newData, $users){
        $diffStr  = $this->lang->my->num."：".$newData->id."被删除";
        return $diffStr;
    }
    /**
     * 获取领导列表和授权管理被授权人员
     * 推送给数字金科app
     */
    public function getLeaderList() {
        $fields = 'id,name,cm,executive,manager1,manager,leader1,leader,groupleader';
        $deptList = $this->dao->select("$fields")->from(TABLE_DEPT)->fetchAll();
        $leader = [];
        foreach ($deptList as $k=>$v){
            $leader[] = $v->cm;
            $leader[] = $v->executive;
            $leader[] = $v->manager1;
            $leader[] = $v->manager;
            $leader[] = $v->leader1;
            $leader[] = $v->leader;
            $leader[] = $v->groupleader;
        }
        $leader = array_values(array_unique(explode(',',str_replace(',,',',',implode(',',array_filter($leader))))));
        $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)
            ->where('deleted')->ne('2')
            ->andWhere('enabled')->eq('2')
            ->andWhere("(permanently = '2' or now() <= endTime)")
            ->orderBy('id')->fetchAll('id');
        $authorization = array_values(array_unique(explode(',',str_replace(',,',',',implode(',',array_filter(array_column($authorizationList,'authorizedPerson')))))));
        $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
        $authorization[] = $reviewer;
        $data = array_filter(array_unique(array_merge($leader,$authorization)));
        return $data;
    }
}
