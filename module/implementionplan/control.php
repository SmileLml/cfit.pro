<?php
class implementionplan extends control
{
    public function __construct()
    {
        parent::__construct();

        session_write_close();
    }

    /**
     * Project: chengfangjinke
     * Method: maintain
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:18
     * Desc: This is the code comment. This method is called maintain.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function maintain( $projectID = 0, $orderBy = 'id_desc', $productID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {

        $this->loadModel('project');
        $uri = $this->app->getURI(true);
        $this->app->session->set('executionList', $uri, 'project');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->app->loadLang('my');
        $this->app->loadLang('product');
        $this->app->loadLang('programplan');
        $project  = $this->project->getById($projectID);


        $from = $this->app->openApp;
        $this->session->set('taskList', $this->app->getURI(true), $from);
        if($from == 'execution') $this->session->set('executionList', $this->app->getURI(true), 'execution');
        if($from == 'project')
        {
            $projects  = $this->project->getPairsByProgram();
            $projectID = $this->project->saveState($projectID, $projects);
            $this->project->setMenu($projectID);
        }


        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $List = $this->implementionplan->getList($orderBy, $pager,$projectID);

        $this->view->title      = $this->lang->implementionplan->common . $this->lang->colon . $this->lang->implementionplan->maintain;
        $this->view->position[] = $this->lang->implementionplan->maintain;

        $this->view->orderBy  = $orderBy;
        $this->view->pager    = $pager;
        $this->view->lists    = $List;
        $this->view->project  = $projectID;
        $this->view->productID  = $productID;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * 上传项目实施计划
     */
    public function uploadPlan($projectID)
    {
        if($_POST)
        {
            $implementionID = $this->implementionplan->create($projectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('implementionplan', $implementionID, 'created');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['closeModal'] = true;
            $response['callback'] = "parent.back()";

            $this->send($response);

        }
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:18
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param string $confirm
     */
    public function delete($implementID)
    {
        if(!empty($_POST))
        {
            if(!$this->post->comment){
                dao::$errors = array('comment' => $this->lang->implementionplan->commentEmpty);
            }
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->dao->update(TABLE_IMPLEMENTIONPLAN)->set('deleted')->eq('deleted')->where('id')->eq($implementID)->exec();
            $this->loadModel('action')->create('implementionplan', $implementID, 'deleted', $this->post->comment);

            /*if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));*/
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['closeModal'] = true;
            $response['callback'] = "parent.back()";

            $this->send($response);
        }

        $implement = $this->implementionplan->getByID($implementID);
        $this->view->actions = $this->loadModel('action')->getList('implementionplan', $implementID);
        $this->view->implement = $implement;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

}
