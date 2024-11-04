<?php

class downloads extends control
{

    /*
     * 定时任务测试时间
     */
    public function testTime()
    {
        $this->downloads->testTime();
        die('success');
    }

    /*
     * 清理无效下载任务
     */
    public function cleanTasks()
    {
        $this->downloads->cleanTasks();
        die('success');
    }
}