<?php
include '../../control.php';
class myReview extends review
{
    /**
     * 项目移动空间.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function projectswap($reviewID){

        $review = $this->review->getByID($reviewID);
        /* Load module. */
        $this->loadModel('program');

        /* Sort project. */
        $programs        = array();
        $orderedProjects = array();
        $objects         = $this->program->getList('all', 'order_asc', null, true);
        $projectCode = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in(array_keys($objects))->fetchPairs();

        foreach($objects as $objectID => $object)
        {
            $object->code = isset($projectCode[$object->id]) ? $projectCode[$object->id] : '';
            if(!empty( $object->code)){
                $object->name =  $object->code."_".$object->name;
            }
            if($object->type == 'program')
            {
                $programs[$objectID] = $object->name;
            }
            else
            {
                $object->parent = $this->program->getTopByID($object->parent);
                $orderedProjects[] = $object;
                unset($objects[$object->id]);
            }
        }

        $projectNames = array();
        $projectNames[''] = '';
        foreach ($orderedProjects as $orderedProject){
            $projectNames[$orderedProject->id] = $orderedProject->name;
        }
        $this->view->projectNames  = $projectNames;
        if($_POST){
            $changes =  $this->review->projectSwap($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes or $this->post->currentComment)
            {

                $actionID = $this->loadModel('action')->create('review', $reviewID, 'projectswap', $this->post->currentComment);
                $this->action->logHistory($actionID, $changes);

            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->review     = $review;
        $this->display();
    }
}