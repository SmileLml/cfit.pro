<?php
class xuanxuanMessage extends messageModel
{
    public function send($objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        /* 获取后台通知中配置的邮件发信。*/
        $firstObjectType = 'set'.ucfirst($objectType).'Mail';
        $this->app->loadLang('custommail');

        //操作用户信息不能直接从app->user里取，从指定的$actor取
        $userInfo = $this->dao->select("*")->from(TABLE_USER)->where('account')->eq($actor)->fetch();
        $messageSetting = $this->config->message->setting;
        if(is_string($messageSetting)) $messageSetting = json_decode($messageSetting, true);
        if(isset($messageSetting['xuanxuan']))
        {
            $messageActions = $messageSetting['xuanxuan']['setting'];
            if(isset($messageActions[$objectType]) and in_array($actionType, $messageActions[$objectType]))
            {
                $this->loadModel('action');
                if($objectType == 'task')
                {
                    $field = 'obj.*,project.name as projectName,execu.name as execuName';
                    $object = $this->dao->select($field)->from($this->config->objectTables[$objectType])->alias('obj')
                        ->beginIF($objectType == 'task')
                        ->leftJoin($this->config->objectTables['project'])->alias('project')->on('project.id = obj.project')
                        ->leftJoin($this->config->objectTables['execution'])->alias('execu')->on('execu.id = obj.execution')
                        ->fi()
                        ->where('obj.id')->eq($objectID)
                        ->fetch();
                }
                else if($objectType == 'story')
                {
                    $field = 'obj.*,product.name as productName';
                    $object = $this->dao->select($field)->from($this->config->objectTables[$objectType])->alias('obj')
                        ->beginIF($objectType == 'story')
                        ->leftJoin($this->config->objectTables['product'])->alias('product')->on('product.id = obj.product')
                        ->fi()
                        ->where('obj.id')->eq($objectID)
                        ->fetch();
                }
                else if($objectType == 'bug')
                {
                    $field = 'obj.*,project.name as projectName,product.name as productName,execu.name as execuName';
                    $object = $this->dao->select($field)->from($this->config->objectTables[$objectType])->alias('obj')
                        ->beginIF($objectType == 'bug')
                        ->leftJoin($this->config->objectTables['project'])->alias('project')->on('project.id = obj.project')
                        ->leftJoin($this->config->objectTables['execution'])->alias('execu')->on('execu.id = obj.execution')
                        ->leftJoin($this->config->objectTables['product'])->alias('product')->on('product.id = obj.product')
                        ->fi()
                        ->where('obj.id')->eq($objectID)
                        ->fetch();
                }
                else
                {
                    $field = 'obj.*';
                    $object = $this->dao->select($field)->from($this->config->objectTables[$objectType])->alias('obj')
                        ->where('obj.id')->eq($objectID)
                        ->fetch();
                }

                $actions = array();
                $field = $this->config->action->objectNameFields[$objectType];
                $title = $objectType == 'mr' ? '' : sprintf($this->lang->message->notifyTitle, $userInfo->realname, $this->lang->action->label->$actionType, 1, $this->lang->action->objectTypes[$objectType]);
                /**
                 * 喧喧标题取邮件标题的内容，如果没有邮件标题扔用之前的
                 */

                $server   = $this->loadModel('im')->getServer('zentao');
                $onlybody = isset($_GET['onlybody']) ? $_GET['onlybody'] : '';
                unset($_GET['onlybody']);
                $url = $server . helper::createLink($objectType, 'view', "id=$objectID", 'html');
                if($this->config->requestType != 'PATH_INFO' && basename($_SERVER['SCRIPT_NAME']) == 'api.php'){
                    $moduleName = $objectType;
                    if(strpos($moduleName, '.') !== false)
                    {
                        list($appName, $moduleName) = explode('.', $moduleName);
                    }
                    else
                    {
                        $appName = $this->app->getAppName();
                    }
                    if(!empty($appName)) $appName .= '/';
                    $url = $server . $this->config->webRoot . $appName . $moduleName . '-' . 'view' . '-' . $objectID . '.html';
                }
                $target = '';
                $subcontent = (object)array('action' => $actionType, 'object' => $objectID, 'objectName' => $object->$field, 'objectType' => $objectType, 'actor' => $userInfo->id, 'actorName' => $userInfo->realname);

                $subcontent->name = $object->$field;
                $subcontent->id = sprintf('%03d', $object->id);
                $subcontent->count = 1;

                if(!in_array($objectType,['task','story','bug'])){
                    $this->loadModel($objectType);
                    if(method_exists($this->$objectType, 'getXuanxuanTargetUser')){
                        $result = $this->$objectType->getXuanxuanTargetUser($object, $objectType, $objectID, $actionType, $actionID, $actor);
                        //特殊情况，强制不触发喧喧
                        if(isset($result['isSend']) && $result['isSend'] == 'no'){
                            return false;
                        }

                        if(isset($result['mailconfig']) && $result['mailconfig']){
                            $mailConf   = $result['mailconfig'];
                            $mailConf   = json_decode($mailConf);
                        }else{
                            $mailConf   = isset($this->config->global->{$firstObjectType}) ? $this->config->global->{$firstObjectType} : '{"mailTitle":"","variables":[],"mailContent":""}';
                            $mailConf   = json_decode($mailConf);

                        }

                        /**
                         * 只有邮件为待办的才会发喧喧通知，特殊情况如需发送喧喧，可以在调用此方法之前设置一个session：isSendXuanxuan
                         * 此方法执行完毕后会将isSendXuanxuan重置为false
                         */
                        $mailTitle = '';

                        if (!isset($_SESSION['isSendXuanxuan']) || !$this->session->isSendXuanxuan){

                            if (strpos($mailConf->mailTitle,'【待办】') === false){

                                return false;
                            }else{

                            }
                        }
                        $this->session->set('isSendXuanxuan', false);
                        $mailTitle = explode('，',vsprintf($mailConf->mailTitle, $mailConf->variables));
                        $mailTitle = $mailTitle[0];
                        if ($title != '' && $mailTitle != ''){
                            $title = $mailTitle;
                        }

                        $target = $result['toList'];
                        if(isset($result['title']) && $result['title']){
                            $title = $result['title'];
                        }
                        if(isset($result['url']) && $result['url']){
                            $url = $result['url'];
                        }
                        if(isset($result['actions']) && $result['actions']){
                            $actions = $result['actions'];
                        }

                        if(isset($result['subcontent']['count']) && $result['subcontent']['count']){
                            $subcontent->count = $result['subcontent']['count'];
                        }
                        if(isset($result['subcontent']['headTitle']) && $result['subcontent']['headTitle']){
                            $subcontent->headTitle = $result['subcontent']['headTitle'];
                        }
                        if(isset($result['subcontent']['headSubTitle']) && $result['subcontent']['headSubTitle']){
                            $subcontent->headSubTitle = $result['subcontent']['headSubTitle'];
                        }
                        if(isset($result['subcontent']['id']) && $result['subcontent']['id']){
                            $subcontent->id = $result['subcontent']['id'];
                        }
                        if(isset($result['subcontent']['parent']) && $result['subcontent']['parent']){
                            $subcontent->parent = $result['subcontent']['parent'];
                        }

                        if(isset($result['subcontent']['parentURL']) && $result['subcontent']['parentURL']){
                            $subcontent->parentURL = "xxc:openUrlInBrowser/" . urlencode($server . $result['subcontent']['parentURL']);
                        }
                        if(isset($result['subcontent']['cardURL']) && $result['subcontent']['cardURL']){
                            $subcontent->cardURL = $result['subcontent']['cardURL'];
                        }
                        if(isset($result['subcontent']['name']) && $result['subcontent']['name']){
                            $subcontent->name = $result['subcontent']['name'];
                        }


                    }
                }else{
                    //处理之前已做好的模块
                    $mailConf   = isset($this->config->global->{$firstObjectType}) ? $this->config->global->{$firstObjectType} : '{"mailTitle":"","variables":[],"mailContent":""}';
                    $mailConf   = json_decode($mailConf);

                    /**
                     * 只有邮件为待办的才会发喧喧通知，特殊情况如需发送喧喧，可以在调用此方法之前设置一个session：isSendXuanxuan
                     * 此方法执行完毕后会将isSendXuanxuan重置为false
                     */
//                    $mailTitle = '';
//                    if (!isset($_SESSION['isSendXuanxuan']) || !$this->session->isSendXuanxuan){
//                        if (strpos($mailConf->mailTitle,'【待办】') === false){
//                            return false;
//                        }
//                    }
//                    $mailTitle = explode('，',vsprintf($mailConf->mailTitle, $mailConf->variables));
//                    $mailTitle = $mailTitle[0];
//                    $this->session->set('isSendXuanxuan', false);
//                    if ($title != '' && $mailTitle != ''){
//                        $title = $mailTitle;
//                    }
                    if(!empty($object->assignedTo)) $target .= $object->assignedTo;
                    if(!empty($object->mailto))     $target .= ",{$object->mailto}";
                }

                if($objectType == 'mr' && !empty($object->createdBy)) $target .= ",{$object->createdBy}";
                $target = trim($target, ',');
                $target = $this->dao->select('id')->from(TABLE_USER)
                    ->where('account')->in($target)
                    ->beginIF($objectType != 'mr')->andWhere('account')->ne($userInfo->account)->fi()
                    ->fetchAll('id');
                $target = array_keys($target);


                if($objectType == 'task')
                {
                    $subcontent->headTitle    = $object->projectName;
                    $subcontent->headSubTitle = $object->execuName;
                    $subcontent->parentType   = 'execution';
                    $subcontent->parent       = $object->execution;
                    $subcontent->parentURL    = "xxc:openUrlInBrowser/" . urlencode($server . helper::createLink('execution', 'task', "id=$object->execution", 'html#app=project'));
//                    $url .= '#app=project';
                    $subcontent->cardURL      = $url;
                    if(strpos($url, 'app=') === false) $url = str_replace('.html', '.html#app=project', $url);
                }
                elseif($objectType == 'story')
                {
                    $subcontent->headTitle  = $object->productName;
                    $subcontent->parentType = 'product';
                    $subcontent->parent     = $object->product;
                    $subcontent->parentURL  = "xxc:openUrlInBrowser/" . urlencode($server . helper::createLink('product', 'browse', "id=$object->product", 'html'));
                    $subcontent->cardURL    = $url;
                }
                elseif($objectType == 'bug')
                {
                    $parentType = empty($object->execuName) ? 'product' : 'project';
                    $parentNameKey = $parentType . 'Name';
                    $subcontent->headTitle    = $object->$parentNameKey;
                    $subcontent->headSubTitle = $object->execuName;
                    $subcontent->parentType   = $parentType;
                    $subcontent->parent       = $object->$parentType;
                    $subcontent->parentURL    = "xxc:openUrlInBrowser/" . urlencode($server . helper::createLink($parentType, 'browse', "id=$subcontent->parent", 'html'));
                    $subcontent->cardURL      = $url;
                }
                else
                {
                    $subcontent->parentType = $objectType;
                }

                $contentData = new stdclass();

                $contentData->title       = $title;
                $contentData->subtitle    = '';
                $contentData->contentType = "zentao-$objectType-$actionType";
                $contentData->parentType  = $subcontent->parentType;
                $contentData->content     = json_encode($subcontent,JSON_UNESCAPED_UNICODE);
                $contentData->actions     = array();
                $contentData->url         = "xxc:openUrlInBrowser/" . urlencode($url);


                $content   = json_encode($contentData,JSON_UNESCAPED_UNICODE);
                $avatarUrl = $server . $this->app->getWebRoot() . 'favicon.ico';
                if($target) $this->loadModel('im')->messageCreateNotify($target, $title, $subtitle = '', $content, $contentType = 'object', $url, $actions = array(), $sender = array('id' => 'zentao', 'realname' => $this->lang->message->sender, 'name' => $this->lang->message->sender, 'avatar' => $avatarUrl));

                if($objectType == 'mr' and is_array($this->lang->message->mr->$actionType) and !empty($object->assignee))
                {
                    $contentData->content = sprintf($this->lang->message->mr->{$actionType}['reviewer'], $object->title);

                    $content = json_encode($contentData);
                    $target  = $this->dao->select('id')->from(TABLE_USER)->where('account')->eq($object->assignee)->fetch('id');
                    if($target) $this->loadModel('im')->messageCreateNotify(array($target), $title, $subtitle = '', $content, $contentType = 'object', $url, $actions = array(), $sender = array('id' => 'zentao', 'realname' => $this->lang->message->sender, 'name' => $this->lang->message->sender, 'avatar' => $avatarUrl));
                }

                if($onlybody) $_GET['onlybody'] = $onlybody;
            }
        }
    }
}
