<?php
/**
 * The control file of measure module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuchun Li <liyuchun@easycorp.ltd>
 * @package     kanban
 * @version     $Id: control.php 4460 2021-10-26 11:03:02Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.net
 */
class measure extends control{
    public function browse(){
        $this->app->loadLang("project");
        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $particDepts = $this->post->particDepts ? $this->post->particDepts   : array();
        $spaceId = $this->post->spaceId ? $this->post->spaceId   : array();
        $isParam = $this->post->isParam ? $this->post->isParam : 0;
        if (!empty($account) && $account[0] == '') unset($account[0]);
        if (!empty($particDepts) && $particDepts[0] <= 0) unset($particDepts[0]);
        if (!empty($spaceId) && $spaceId[0] <= 0) unset($spaceId[0]);
        $spaceList = $this->loadModel('kanban')->getAllSpaceCard();
        $spaceData = [];
        foreach ($spaceList as $sk=>$sv) {
            $spaceData[$sk] = $sv->name;
        }

        $usersList = $this->getUsers();
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        //有搜索条件再查询
        if ($isParam > 0) {
            // 获取部门、项目团队成员、用户工作量数据。
//            $spaceId = [16,2];
            $workloadTotal = $this->measure->getEffortBySpace($spaceId, $begin, $end,$account,$particDepts);
            // 只要报工过的人就算。
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            $amount = array('count' => '合计', 'depts'=>0,'user' => 0, 'total' => 0, 'perMonth' => 0);
            $participants = array('' => '');
            $depts = [];
            $space = [];
            foreach ($workloadTotal as $k => $v) {
                $space[$v->project] = $v->project;
                $participants[$v->account] = $usersList[$v->account];
                $depts[$v->deptID] = $deptMap[$v->deptID];
                if ($v->account != '') {
                    if (!empty($account) and !in_array($v->account, $account)) continue;
                    if (!empty($particDepts) and !in_array($v->deptID, $particDepts)) continue;
                    $v->deptName = zget($deptMap, $v->deptID, '');
                    $v->total = $v->workload;
                    $v->perMonth = round(($v->total / $spaceList[$v->project]->workHours) / 8, 2);
                    $amount['total'] += $v->total;
                    $amount['perMonth'] += $v->perMonth;
                }
            }
            rsort($space);
            $realDepts = array_column($workloadTotal,'deptID');
            $new = [];
            $amount['depts'] = count(array_unique($realDepts));
            foreach ($space as $k2=>$v2) {
                foreach ($depts as $dk=>$dept) {
                    foreach ($workloadTotal as $uk=>$uv){
                        if ($uv->project == $v2 && $dk == $uv->deptID){
                            $new[$v2][$dk][] = $uv;
                        }
                    }
                }
            }
            $users = [];
            //整理项目数据
            foreach ($new as $nk=>$nv) {
                $array['total'] = 0;
                foreach ($nv as $item) {
                    $array['total'] += count($item);
                }
                $array['project'] = $nk;
                $array['projectName'] = $spaceData[$nk];
                $array['data'] = $nv;
                $users[] = $array;
            }
            foreach ($users as $uk=>$user) {
                foreach ($user['data'] as $dk=>$dv) {
                    $arr['total'] = count($dv);
                    $arr['deptId'] = $dk;
                    $arr['data'] = $dv;
                    $users[$uk]['data'][$dk] = $arr;
                }
            }
            $amount['user'] = count($participants) - 1;//数组第一个元素为追加的空元素，所以求总数-1
            $param = json_encode(array('spaceId'=>$spaceId,'begin' => $begin, 'end' => $end, 'account' => $account,'particDepts'=>$particDepts));
            $this->view->members      = $users;
            $this->view->amount       = $amount;
            $this->view->param        = helper::safe64Encode($param);
        }
        $this->view->depts        = $deptMap;
        $this->view->participants = $usersList;
        $menu = explode(',',$this->lang->measure->leftMenu[0]);
        $this->view->spaceData        = $spaceData;
        $this->view->spaceId        = $spaceId;
        $this->view->particDepts        = $particDepts;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->account      = $account;
        $this->view->isParam     = $isParam;
        $this->view->title     = $menu[2];
        $this->display();
    }
    //看板参与人员工作量报表
    public function kanbanparticwork(){
        $this->app->loadLang("project");
        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $particDepts = $this->post->particDepts ? $this->post->particDepts   : array();
        $spaceId = $this->post->spaceId ? $this->post->spaceId   : array();
        $kanbanId = $this->post->kanbanId ? $this->post->kanbanId   : array();
        $isParam = $this->post->isParam ? $this->post->isParam : 0;
        if (!empty($account) && $account[0] == '') unset($account[0]);
        if (!empty($particDepts) && $particDepts[0] <= 0) unset($particDepts[0]);
        if (!empty($spaceId) && $spaceId[0] <= 0) unset($spaceId[0]);
        if (!empty($kanbanId) && $kanbanId[0] <= 0) unset($kanbanId[0]);
        $spaceList = $this->loadModel('kanban')->getAllSpaceCard($spaceId);
        $spaceData = [];
        $kanbans = [];
        foreach ($spaceList as $sk=>$sv) {
            $spaceData[$sk] = $sv->name;
            if (!empty($sv->kanbans)){
                foreach ($sv->kanbans as $kanban){
                    $kanbans[$kanban->id] = $sv->name.'/'.$kanban->name;
                }
            }
        }
        $usersList = $this->getUsers();
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        //有搜索条件再查询
        if ($isParam > 0){
            $workloadTotal = $this->measure->getEffortBySpaceKanban($spaceId,$kanbanId, $begin, $end, $account, $particDepts);
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            // 只要报工过的人就算。
            $amount = array('count' => '合计', 'kanbans'=>0,'depts'=>0,'user' => 0, 'total' => 0, 'perMonth' => 0);
            $participants = array('' => '');
            $depts = array(''=>'');
            $space = [];
            foreach ($workloadTotal as $wk=>$wv) {
                $space[$wv->project] = $wv->project;
                $depts[$wv->deptID] = $deptMap[$wv->deptID];
                $participants[$wv->account] = $usersList[$wv->account];
                $depts[$wv->deptID] = $deptMap[$wv->deptID];
                if ($wv->account != '') {
                    if (!empty($account) and !in_array($wv->account, $account)) continue;
                    if (!empty($particDepts) and !in_array($wv->deptID, $particDepts)) continue;
                    $wv->deptName = zget($deptMap, $wv->deptID, '');
                    $wv->total = $wv->workload;
                    $wv->perMonth = round(($wv->total / $spaceList[$wv->project]->workHours) / 8, 2);
                    $amount['total'] += $wv->total;
                    $amount['perMonth'] += $wv->perMonth;
                }
            }
            $kanbanIdResult = array_column($workloadTotal,'execution');
            $kanbanIdUnique = array_unique($kanbanIdResult);
            $realDepts = array_column($workloadTotal,'deptID');
            $new = [];
            $amount['depts'] = count($realDepts);
            foreach ($space as $k2=>$v2) {
                foreach ($kanbanIdUnique as $k3=>$v3) {
                    foreach ($depts as $dk=>$dept) {
                        foreach ($workloadTotal as $wk=>$wv){
                            if ($wv->project == $v2 && $dk == $wv->deptID && $wv->execution == $v3){
                                $new[$v2][$v3][$dk][] = $wv;
                            }
                        }
                    }
                }
            }
            $users = [];
            //整理项目数据
            foreach ($new as $nk=>$nv) {
                $array['total'] = 0;
                foreach ($nv as $item) {
                    foreach ($item as $item2) {
                        $array['total'] += count($item2);
                    }
                }
                $array['project'] = $nk;
                $array['projectName'] = $spaceData[$nk];
                $array['data'] = $nv;
                $users[] = $array;
            }
            foreach ($users as $uk=>$uv) {
                foreach ($uv['data'] as $dk=>$dv) {
                    foreach ($dv as $dk2=>$dv2) {
                        $users[$uk]['data'][$dk]['total'] = 0;
                        foreach ($workloadTotal as $wk=>$wv) {
                            if ($wv->execution == $dk){
                                $users[$uk]['data'][$dk]['total'] ++;
                                $users[$uk]['data'][$dk]['kanban'] = $dk;
                                $users[$uk]['data'][$dk]['data'][$dk2] = $dv2;
//                                a($users[$uk]['data'][$dk]);exit;
                                unset($users[$uk]['data'][$dk][$dk2]);
                            }
                        }
                    }
                }
            }
            $amount['user'] = count($participants) - 1;
            $kanbanIdResult = array_column($workloadTotal,'execution');
            $kanbanIdUnique = array_unique($kanbanIdResult);
            $realDepts = array_column($workloadTotal,'deptID');
            $amount['depts'] = count(array_unique($realDepts));
            $amount['kanbans'] = count($kanbanIdUnique);
            $param = json_encode(array('spaceId'=>$spaceId,'kanbanId'=>$kanbanId,'begin' => $begin, 'end' => $end, 'account' => $account,'particDepts'=>$particDepts));
            $this->view->param = helper::safe64Encode($param);
            $this->view->members      = $users;
            $this->view->amount       = $amount;

        }
        $this->view->kanbans        = $kanbans;
        $this->view->participants = $usersList;
        $this->view->depts        = $deptMap;
        $menu = explode(',',$this->lang->measure->leftMenu[1]);
        $this->view->spaceData        = $spaceData;
        $this->view->spaceId        = $spaceId;
        $this->view->kanbanId        = $kanbanId;
        $this->view->particDepts        = $particDepts;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->account      = $account;
        $this->view->isParam     = $isParam;
        $this->view->title     = $menu[2];
        $this->display();
    }
    //人员工作量明细
    public function particworkdetail(){
        $this->app->loadLang("project");
        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $particDepts = $this->post->particDepts ? $this->post->particDepts   : array();
        $spaceId = $this->post->spaceId ? $this->post->spaceId   : array();
        $kanbanId = $this->post->kanbanId ? $this->post->kanbanId   : array();
        $isParam = $this->post->isParam ? $this->post->isParam : 0;
        if (!empty($account) && $account[0] == '') unset($account[0]);
        if (!empty($particDepts) && $particDepts[0] <= 0) unset($particDepts[0]);
        if (!empty($spaceId) && $spaceId[0] <= 0) unset($spaceId[0]);
        if (!empty($kanbanId) && $kanbanId[0] <= 0) unset($kanbanId[0]);
        $spaceList = $this->loadModel('kanban')->getAllSpaceCard($spaceId);
        $spaceData = [];//空间数据 id=>name
        $kanbans = [];//看板数据 id=>name
        $kanbanIdList = [];//看板id
        foreach ($spaceList as $sk=>$sv) {
            $spaceData[$sk] = $sv->name;
            if (isset($sv->kanbans)){
                foreach ($sv->kanbans as $kanban) {
                    $kanbanIdList[] = $kanban->id;
                }
            }
            if (!empty($sv->kanbans)){
                foreach ($sv->kanbans as $kanban){
                    $kanbans[$kanban->id] = $sv->name.'/'.$kanban->name;
                }
            }

        }
        $usersList = $this->getUsers();
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        //有搜索条件再查询
        if ($isParam > 0) {
//            $spaceId = [16];
            $workloadTotal = $this->measure->getEffortDetail($spaceId, $kanbanId, $begin, $end, $account, $particDepts);
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            $amount = array('count' => $spaceNum, 'kanbans'=>0,'depts'=>0,'user' => 0, 'total' => 0, 'perMonth' => 0,'cards'=>0,'dateNum'=>0);
            $cards = $this->loadModel('kanban')->getCardsBykanbanIds($kanbanIdList,'id,name,kanban');
            $participants = [''=>''];
            $depts = [''=>''];
            foreach ($workloadTotal as $wk=>$wv) {
                $depts[$wv->deptID] = $deptMap[$wv->deptID];
                $workloadTotal[$wk]->spaceName = $spaceData[$wv->project];
                $workloadTotal[$wk]->kanbanName = $kanbans[$wv->execution];
                $workloadTotal[$wk]->deptName = $deptMap[$wv->deptID];
                $amount['total'] += $wv->workload;
                $amount['dateNum'] += 1;
                foreach ($cards as $card) {
                    if ($card->id == $wv->objectID){
                        $workloadTotal[$wk]->cardName = $card->name;
                    }
                }
                $participants[$wv->account] = $usersList[$wv->account];
                $workloadTotal[$wk]->realName = $usersList[$wv->account];
            }
            $amount['user'] = count($participants) - 1;
            $kanbanIdResult = array_column($workloadTotal,'execution');
            $kanbanIdUnique = array_unique($kanbanIdResult);
            $realDepts = array_column($workloadTotal,'deptID');
            $amount['depts'] = count(array_unique($realDepts));
            $amount['kanbans'] = count($kanbanIdUnique);
            $realcards = array_column($workloadTotal,'objectID');
            $amount['cards'] = count(array_unique($realcards));
            $this->view->members      = $workloadTotal;
            $this->view->particDepts      = $particDepts;
            $this->view->amount       = $amount;
            $this->view->kanbans        = !empty($spaceId)?$kanbans:[];
        }
        $this->view->depts        = $deptMap;
        $this->view->participants = $usersList;

        $menu = explode(',',$this->lang->measure->leftMenu[2]);
        $this->view->kanbans        = $kanbans;
        $this->view->spaceData        = $spaceData;
        $this->view->spaceId        = $spaceId;
        $this->view->particDepts        = $particDepts;
        $this->view->kanbanId        = $kanbanId;

        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->account      = $account;
        $this->view->isParam     = $isParam;
        $this->view->title     = $menu[2];
        $param = json_encode(array('spaceId'=>$spaceId,'kanbanId'=>$kanbanId,'begin' => $begin, 'end' => $end, 'account' => $account,'particDepts'=>$particDepts));
        $this->view->param        = helper::safe64Encode($param);
        $this->display();
    }
    public function ajaxGetkanban(){
        $spaceStr = $this->post->spaceId ? rtrim($this->post->spaceId,','):'';
        $source = $this->post->source ? $this->post->source : '';
        if ($spaceStr == ''){
            echo html::select('kanbanId[]', [], [], "class='form-control chosen' multiple='multiple'");exit;
        }
        $spaceId = explode(',',$spaceStr);
        $spaceList = $this->loadModel('kanban')->getAllSpaceCard();
        $kanbans = [''=>''];
        foreach ($spaceList as $space) {
            if (in_array($space->id,$spaceId)){
                if (!empty($space->kanbans)){
                    foreach ($space->kanbans as $kanban){
                        if ($source == 'selectSpace'){
                            $kanbans[$kanban->id] = $kanban->name;
                        }else{
                            $kanbans[$kanban->id] = $space->name.'/'.$kanban->name;
                        }
                    }
                }
            }
        }
        if ($source == 'selectSpace'){
            echo html::select('kanbanId', $kanbans, '', "class='form-control chosen' data-drop_direction='down' onchange='getGroupLane()'");
        }else{
            echo html::select('kanbanId[]', $kanbans, '', "class='form-control chosen' multiple='multiple'");
        }

    }
    public function exportbrowse($params){
        $params = json_decode(helper::safe64Decode($params),true);
        /**
         * 循环参数数组$key值为变量名
         * 例：{"spaceId":["16","2"],"begin":"","end":"","account":[],"particDepts":[]}
         * $spaceId = [16,2]
         * $begin = ""
         */
        foreach ($params as $key=>$item) {
            ${$key} = $item;
        }

        if ($_POST){
            $this->loadModel('file');
            $this->loadModel('productenroll');
            $measureLang   = $this->lang->measure;
            $measureConfig = $this->config->measure;

            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $measureConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($measureLang->$fieldName) ? $measureLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $spaceList = $this->loadModel('kanban')->getAllSpaceCard();
            $spaceData = [];
            foreach ($spaceList as $sk=>$sv) {
                $spaceData[$sk] = $sv->name;
            }
            $usersList = $this->getUsers();
            $deptMap = $this->loadModel('dept')->getOptionMenu();
            $workloadTotal = $this->measure->getEffortBySpace($spaceId, $begin, $end,$account,$particDepts);
            // 只要报工过的人就算。
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            $amount = array('projectName' => '合计', 'kanbanName'=>0, 'deptName'=>0,'realName' => 0, 'total' => 0, 'perMonth' => 0);
            $participants = array('' => '');
            $depts = [];
            foreach ($workloadTotal as $k => $v) {
                $participants[$v->account] = $usersList[$v->account];
                $v->realName = $usersList[$v->account];
                $depts[$v->deptID] = $deptMap[$v->deptID];
                if ($v->account != '') {
                    $v->projectName = htmlspecialchars_decode($spaceData[$v->project]);
                    $v->deptName = zget($deptMap, $v->deptID, '');
                    $v->total = $v->workload;
                    $v->perMonth = round(($v->total / $spaceList[$v->project]->workHours) / 8, 2);
                    $amount['total'] += $v->total;
                    $amount['perMonth'] += $v->perMonth;
//                    $amount['user']     += 1;
                }
            }
            $amount['realName'] = count($participants) - 1;
            $realDepts = array_column($workloadTotal,'deptID');
            $amount['deptName'] = count(array_unique($realDepts));
            $workloadTotal[] = (object)$amount;
            $this->post->set('fields', $fields);
            $this->post->set('rows', $workloadTotal);
            $this->post->set('kind', 'measure');
            $this->post->set('width',   $this->config->measure->export->width);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $menu = explode(',',$this->lang->measure->leftMenu[0]);
        $this->view->fileName        = $menu[2];
        $this->view->allExportFields = $this->config->measure->list->exportFields;
        $this->view->customExport    = true;
        $this->display();

    }
    //导出看板参与人员工作量报表
    public function exportparticwork($params){
        $params = json_decode(helper::safe64Decode($params),true);
        /**
         * 循环参数数组$key值为变量名
         * 例：{"spaceId":["16","2"],"begin":"","end":"","account":[],"particDepts":[]}
         * $spaceId = [16,2]
         * $begin = ""
         */
        foreach ($params as $key=>$item) {
            ${$key} = $item;
        }
        if ($_POST){
            $this->loadModel('file');
            $this->loadModel('productenroll');
            $measureLang   = $this->lang->measure;
            $measureConfig = $this->config->measure;

            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $measureConfig->list->exportKanbanWorkFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($measureLang->$fieldName) ? $measureLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $spaceList = $this->loadModel('kanban')->getAllSpaceCard();
            $spaceData = [];
            $kanbans = [];
            foreach ($spaceList as $sk=>$sv) {
                $spaceData[$sk] = $sv->name;
                if (!empty($sv->kanbans)){
                    foreach ($sv->kanbans as $kanban){
                        $kanbans[$kanban->id] = $sv->name.'/'.$kanban->name;
                    }
                }
            }
            $usersList = $this->getUsers();
            $deptMap = $this->loadModel('dept')->getOptionMenu();
            $workloadTotal = $this->measure->getEffortBySpaceKanban($spaceId,$kanbanId, $begin, $end, $account, $particDepts);
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            // 只要报工过的人就算。
            $amount = array('projectName' => '合计', 'deptName'=>0,'realName' => 0, 'total' => 0, 'perMonth' => 0);
            $participants = array('' => '');
            $depts = array(''=>'');
            foreach ($workloadTotal as $wk=>$wv) {
                $participants[$wv->account] = $usersList[$wv->account];
                $wv->realName = $usersList[$wv->account];

                $depts[$wv->deptID] = $deptMap[$wv->deptID];
                if ($wv->account != '') {
                    $wv->projectName = htmlspecialchars_decode($spaceData[$wv->project]);
                    $wv->kanbanName = htmlspecialchars_decode($kanbans[$wv->execution]);
                    if (!empty($account) and !in_array($wv->account, $account)) continue;
                    if (!empty($particDepts) and !in_array($wv->deptID, $particDepts)) continue;
                    $wv->deptName = zget($deptMap, $wv->deptID, '');
                    $wv->total = $wv->workload;
                    $wv->perMonth = round(($wv->total / $spaceList[$wv->project]->workHours) / 8, 2);
                    $amount['total'] += $wv->total;
                    $amount['perMonth'] += $wv->perMonth;
                }
            }
            $amount['realName'] = count($participants) - 1;
            $kanbanIdResult = array_column($workloadTotal,'execution');
            $kanbanIdUnique = array_unique($kanbanIdResult);
            $realDepts = array_column($workloadTotal,'deptID');
            $amount['deptName'] = count(array_unique($realDepts));
            $amount['kanbanName'] = count($kanbanIdUnique);
            $workloadTotal[] = (object)$amount;
            $this->post->set('fields', $fields);
            $this->post->set('rows', $workloadTotal);
            $this->post->set('kind', 'measure');
            $this->post->set('width',   $this->config->measure->export->width);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }


        $menu = explode(',',$this->lang->measure->leftMenu[1]);
        $this->view->fileName        = $menu[2];
        $this->view->allExportFields = $this->config->measure->list->exportKanbanWorkFields;
        $this->view->customExport    = true;
        $this->display();
    }
    public function exportparticworkdetail($params){
        $params = json_decode(helper::safe64Decode($params),true);
        /**
         * 循环参数数组$key值为变量名
         * 例：{"spaceId":["16","2"],"begin":"","end":"","account":[],"particDepts":[]}
         * $spaceId = [16,2]
         * $begin = ""
         */
        foreach ($params as $key=>$item) {
            ${$key} = $item;
        }

        if ($_POST){
            $this->loadModel('file');
            $this->loadModel('productenroll');
            $measureLang   = $this->lang->measure;
            $measureConfig = $this->config->measure;

            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $measureConfig->list->exportKanbanWorkDetails);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($measureLang->$fieldName) ? $measureLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $spaceList = $this->loadModel('kanban')->getAllSpaceCard();
            $spaceData = [];//空间数据 id=>name
            $kanbans = [];//看板数据 id=>name
            $kanbanIdList = [];//看板id
            foreach ($spaceList as $sk=>$sv) {
                $spaceData[$sk] = $sv->name;
                if (isset($sv->kanbans)){
                    foreach ($sv->kanbans as $kanban) {
                        $kanbans[$kanban->id] = $kanban->name;
                        $kanbanIdList[] = $kanban->id;
                    }
                }
            }
            $usersList = $this->getUsers();
            $deptMap = $this->loadModel('dept')->getOptionMenu();
//            $spaceId = [16];
            $workloadTotal = $this->measure->getEffortDetail($spaceId,$kanbanId, $begin, $end, $account, $particDepts);
            $spaceNum = count(array_unique(array_column($workloadTotal,'project')));
            $amount = array('spaceID' => "合计", 'projectName'=>$spaceNum,'kanbanID'=>0,'kanbanName'=>0,'cardID'=>0,'cardName'=>0,'deptName'=>0,'realName' => 0, 'workhours' => 0, 'workDate' => 0,'createdDate'=>0);
            $cards = $this->loadModel('kanban')->getCardsBykanbanIds($kanbanIdList,'id,name,kanban');
            $participants = [''=>''];
            $depts = [''=>''];
            $data = [];
            foreach ($workloadTotal as $wk=>$wv) {
                $depts[$wv->deptID] = $deptMap[$wv->deptID];

                $info = new stdClass();
                $info->spaceID = $wv->project;
                $info->projectName = htmlspecialchars_decode($spaceData[$wv->project]);
                $info->kanbanID = $wv->execution;
                $info->kanbanName = htmlspecialchars_decode($kanbans[$wv->execution]);
                $info->cardID = $wv->objectID;
                $info->workLogs = $wv->work;
                $info->deptName = $deptMap[$wv->deptID];
                $info->workhours = $wv->workload;
                $info->workDate = $wv->date;
                $info->createdDate = $wv->realDate;
                foreach ($cards as $card) {
                    if ($card->id == $wv->objectID){
                        $info->cardName = htmlspecialchars_decode($card->name);
                    }
                }

                $participants[$wv->account] = $usersList[$wv->account];
                $info->realName = $usersList[$wv->account];
                $amount['workhours'] += $wv->workload;
                $amount['workDate'] += 1;
                $data[] = $info;
            }
            $amount['workDate'] = "-";
            $amount['createdDate'] = $amount['workDate'];
            $amount['realName'] = count($participants) - 1;
            $kanbanIdResult = array_column($workloadTotal,'execution');
            $kanbanIdUnique = array_unique($kanbanIdResult);
            $realDepts = array_column($workloadTotal,'deptID');
            $amount['deptName'] = count(array_unique($realDepts));
            $amount['kanbanID'] = count($kanbanIdUnique);
            $amount['kanbanName'] = $amount['kanbanID'];
            $realcards = array_column($workloadTotal,'objectID');
            $amount['cardID'] = count(array_unique($realcards));
            $amount['cardName'] = $amount['cardID'];
            $amount['workLogs'] = $amount['workDate'];
            $data[] = (object)$amount;
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', 'measure');
            $this->post->set('width',   $this->config->measure->export->width);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $menu = explode(',',$this->lang->measure->leftMenu[2]);
        $this->view->fileName        = $menu[2];
        $this->view->allExportFields = $this->config->measure->list->exportKanbanWorkDetails;
        $this->view->customExport    = true;
        $this->display();

    }
    public function getUsers(){
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        return $users;
    }
}
