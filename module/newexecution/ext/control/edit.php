<?php
include '../../control.php';
class myNewExecution extends newexecution
{
    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:28
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $executionID
     * @param string $action
     * @param string $extra
     */
    public function edit($executionID, $action = '', $extra = '')
    {
        // 避免计划和任务同时高亮。
        unset($this->lang->waterfall->menu->task['subModule']);

        if($_POST)
        {
            $changes = $this->newexecution->edit($executionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('newexecution', $executionID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "executionID=$executionID");

            $this->send($response);
        }

        $executionID = (int)$executionID;
        $execution   = $this->newexecution->getById($executionID, true);
        $execution->realBegan = $execution->realBegan == '0000-00-00' ? '' : $execution->realBegan;
        $execution->realEnd   = $execution->realEnd   == '0000-00-00' ? '' : $execution->realEnd;

        $this->loadModel('project')->setMenu($execution->project);
        $this->view->title = $this->lang->execution->view;
        $this->app->loadLang('stage');
        $this->lang->stage->typeList[''] = '';
        $this->view->typelist = array(''=>'') + $this->lang->stage->typeList;
        $this->view->execution = $execution;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->display();
    }
}
