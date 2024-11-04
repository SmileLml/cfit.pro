<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * 更新附件
     * @param $buildID
     */
    public function editfiles($buildID){

      //  $this->view->title      = $this->lang->review->editNodeUsers;
      //  $this->view->position[] = $this->lang->review->editNodeUsers;
        $this->app->loadLang('review');
        $this->app->loadConfig('review');
        $build = $this->build->getByID($buildID);
        if($_POST ||$_FILES){
            $changes =  $this->build->editFilesByID($buildID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('build', $buildID, 'renewfile', '');
                $this->action->logHistory($actionID, $changes);

            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->build     = $build;
        $this->display();
    }
}