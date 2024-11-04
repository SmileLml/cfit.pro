<?php
include '../../control.php';
class myProjectrelease extends projectrelease
{
    /**
     * Project: chengfangjinke
     * Method: publish
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called publish.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $releaseID
     */
    public function publish($releaseID)
    {
        $release = $this->projectrelease->getByID($releaseID);
        if($_POST)
        {
            $result = $this->projectrelease->publish($releaseID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('projectrelease', $releaseID, 'publish', '');
            if($result['result']) $this->projectplan->sendmail($planID, $actionID, $result['grade'], $result['result']);

            $response['result']  = 'success';
            $response['message'] = $this->lang->release->sendSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->project->setMenu($release->project);

        $this->view->title   = $this->lang->release->publish;
        $this->view->release = $release;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }
}
