<?php
include '../../control.php';
class myReview extends review
{
    /**
     * 自动处理评审确定初审结论
     */
    public function autodealreviewmain(){
        $this->review->autodealreview();
    }

}
