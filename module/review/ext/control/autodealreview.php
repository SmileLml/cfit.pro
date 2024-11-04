<?php
include '../../control.php';
class myReview extends review
{
    /**
     * 自动处理评审
     */
    public function autodealreview(){
        $this->review->autodealreview();
    }

}
