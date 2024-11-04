<?php
class requirementChangeModel extends model
{

    /**
     * 获取变更单数据集
     */
    public function getById($id)
    {
        return $this->dao->findByID($id)->from(TABLE_REQUIREMENTCHANGE)->fetch();
    }

    /**
     * 根据变更单唯一标识获取需求变更单
     */
    public function getByChangeNumber($changeNumber)
    {
        return $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGE)->where('changeNumber')->eq($changeNumber)->fetch();
    }

    /**
     * 根据业务需求唯一标识获取素有需求变更单
     */
    public function getByDemandNumber($demandNumber)
    {
        return $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGE)->where('demandNumber')->eq($demandNumber)->orderBy('createdDate asc')->fetchAll();
    }
    /**
     * @param $changeId
     * 根据外部需求条目编号 获取变更单
     */
    public function getChangesByEntries($entriesCode){
        $sql = "select * from ".TABLE_REQUIREMENTCHANGE." where find_in_set('".$entriesCode."',changeEntry)";
        $requirements = $this->dao->query($sql)->fetchall();
        return $requirements;
//        return $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGE)->where('changeEntry')->eq($entriesCode)->orderBy('createdDate asc')->fetchAll();
    }
    //发送生产变更单邮件
    public function sendmail($changeId,$actionID){
        $data = $_POST;
        $data['changeEntry'] = implode(',',$data['changeEntry']);
        $data['createdDate'] = date('Y-m-d H:i:s',time());
        $data['createdBy'] = 'qz';
        /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
        $this->loadModel('mail');
        $changeInfo = $this->getById($changeId);
        $opinionInfo = $this->loadModel('opinion')->getByCode($changeInfo->demandNumber);
        $assignedTo = trim($opinionInfo->assignedTo.','.$opinionInfo->dealUser,',');//需求负责人+待处理人
        $requireList = $this->loadModel('requirement')->getByOpinion($opinionInfo->id);
        $str = "";
        $requireCode = "";
        foreach ($requireList as $rk=>$rv) {
            $requireCode .= $rv->code.',';
            if ($rv->productManager){
                $str .= $rv->productManager.',';
            }
            if ($rv->dealUser){
                $str .= $rv->dealUser.',';
            }
        }
        $requires = $this->loadModel('requirement')->getByCodes($data['changeEntry']);
        $requireCode = implode(',',array_column($requires,'code'));
//        $requireCode = trim($requireCode,',');
        $toList = trim($assignedTo.','.$str,',');
        $toList = implode(',',array_unique(explode(',',$toList)));

        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setRequirementchangeMail) ? $this->config->global->setRequirementchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        /* 处理邮件发信的标题和日期。*/
//        $bestDate  = empty($opinion->changedDate) ? '' : $requirement->changedDate;
        $mailTitle = $mailConf->mailTitle;

        /* Get action info. */
        /* 当前需求意向的操作记录。*/
//        $action = $this->loadModel('action')->getById($actionID);
//        $history = $this->action->getHistory($actionID);
//        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        /* 获取当前模块路径，然后获取发信模板，为发信模板赋值。*/
        $modulePath = $this->app->getModulePath($appName = '', 'requirementchange');

        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 获取发信人和抄送人数据。*/
        if (!$toList) return;
        $ccList = "";

        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->mail->send($toList, $mailConf->mailTitle, $mailContent, $ccList);
        if ($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }
}
