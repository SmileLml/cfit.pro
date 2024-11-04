<?php
include '../../control.php';
class myResidentwork extends residentwork
{
    public function workExport($browseType = '',$param = 0, $orderBy = 'id_desc'){
        $this->app->loadLang("residentsupport");
        if ($_POST){
            $users = $this->loadModel('user')->getPairs('noletter');
            $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
            $users = array_merge($users, $outsideList1, $outsideList2);

            $type = $this->lang->residentsupport->typeList;
            $subType = $this->lang->residentsupport->subTypeList;
            $area = $this->lang->residentsupport->areaList;
            $dateType = $this->lang->residentsupport->dateTypeList;
            $depts = $this->loadModel('dept')->getOptionMenu();
            $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
            $residentSupports = $this->residentwork->getWorkList($browseType,$queryID, $orderBy);
            $data = [];
            foreach ($residentSupports as $k=>$v) {

                $dutyUserDeptStr = '';
                $dutyUserDeptArr = explode(',',$v->realDutyuserDept);
                $dutyUserDeptList = getArrayValuesByKeys($depts, $dutyUserDeptArr);
                $dutyUserDeptStr .= implode(',', array_unique($dutyUserDeptList));
                $logSource = "排班计划";
                if ($v->dayId == 0){
                    $logSource = "用户创建";
                }
                $arr = [
                    'dutyDate' => $v->dutyDate,
                    'type' => $type[$v->type],
                    'subType' => $subType[$v->subType],
                    'logSource' => $logSource,
                    'pushTitle' => $this->lang->residentwork->logPushStatusArray[$v->pushStatus],
                    'dutyUserDept' => $dutyUserDeptStr,
                    'actualLeader' => isset($v->groupLeader) ? $users[$v->groupLeader] : '',
                    'area' => isset($v->area) ? $area[$v->area] : '',
                    'dateType' => isset($v->dateType) ? $dateType[$v->dateType] : '',
                    'isEmergency' => isset($v->isEmergency) ? $v->isEmergency : '',
                    'remark' => isset($v->remark) ? strip_tags($this->residentwork->toHtml($v->remark)) : '',
                    'logs' => isset($v->logs) ? strip_tags($this->residentwork->toHtml($v->logs)) : '',
                    'warnLogs' => isset($v->warnLogs) ? strip_tags($this->residentwork->toHtml($v->warnLogs)) : '',
                    'analysis' => isset($v->analysis) ? strip_tags($this->residentwork->toHtml($v->analysis)) : '',
                    'createdBy' => isset($v->editedBy) ? $v->editedBy : '',
                    'createdDate' => isset($v->editedDate) ? $v->editedDate : '',
                    'user' => '',
                ];
                if ($arr['isEmergency'] == "") {
                    $arr['isEmergency'] = "";
                }
                if ($arr['isEmergency'] == 1) {
                    $arr['isEmergency'] = "是";
                }
                if ($arr['isEmergency'] == 2) {
                    $arr['isEmergency'] = "否";
                }
                $userArr = explode(',',$v->dutyUser);
                foreach ($userArr as $uv) {
                    $arr['user'] .= $users[$uv].',';
                }
                $arr['user'] = rtrim($arr['user'] ,',');
                $data[] = (object)$arr;
            }
            foreach($this->config->residentwork->export->templateFields2 as $field) $fields[$field] = $this->lang->residentwork->exportFileds->$field;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'residentwork');
            $this->post->set('rows', $data);
            $this->post->set('width',   $this->config->residentwork->export->width);

            $this->post->set('fileName', $_POST['fileName']);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->title = $this->lang->residentsupport->common;

        $this->display();
    }

}