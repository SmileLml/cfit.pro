<?php

include '../../control.php';
class myReview extends review
{
    /**
     * Project: chengfangjinke
     * Method: workloadDetails
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called workloadDetails.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $reviewID
     * @param int $consumedID
     */
    public function workloadDetails($reviewID = 0, $consumedID = 0)
    {
        $this->view->title    = $this->lang->review->workloadDetails;
        $this->view->users    = $this->loadModel('user')->getPairs('noletter');
        $this->view->details  = $this->loadModel('consumed')->getWorkloadDetails($consumedID);
        $this->display();

    }


}