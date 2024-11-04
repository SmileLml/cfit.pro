<?php
include '../../control.php';
class myReview extends review
{
    /**
     * review a review 只用来做判断权限.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function editNodeUsers($reviewID, $nodeId){
        $this->setEditNodeUsers($reviewID, $nodeId);
        $this->display();
    }
}