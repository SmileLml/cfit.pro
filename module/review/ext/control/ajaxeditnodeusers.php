<?php
include '../../control.php';
class myReview extends review
{
    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function ajaxEditNodeUsers($reviewID, $nodeId){
        $this->setEditNodeUsers($reviewID, $nodeId);
        $this->display();
    }
}