<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class autoDealReview extends control
{
    public function dealReview()
    {
        return $this->loadModel('review')->autodealreview(); //正常调用模块及方法
    }
    public function dealReviewMain()
    {
        return $this->loadModel('review')->autodealreview(); //正常调用模块及方法
    }
}
$lock = getTimeLock('autoDealReview', 20); //锁定防止重复
$dealReviews = new autoDealReview();
$data = $dealReviews->dealReview(); //执行
saveLog($data, 'autoDealReview');
$data = $dealReviews->dealReviewMain(); //执行
saveLog($data, 'autoDealReview');
unlock($lock); //解除锁定