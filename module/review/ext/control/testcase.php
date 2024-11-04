<?php
include '../../control.php';
class myReview extends review
{
    //审核的各种测试方法
    public function testCase(){
        //新增多个审核节点
//        $objectType = 'reviewtest';
//        $objectID = 11;
//        $version = 1;
//        $reviewNodes = [
//            array(
//                'reviewers' => 'zhangsan',        //必填 多个人用数组，单个人可以数组也可以字符串
//                //'status'    => '',           //非必填
//                //'stage'     => '',    //非必填
//                //'nodeCode'  => '',  //非必填
//            ),
//            array(
//                'reviewers' => array('lisi', 'wangwu'),        //必填 单个人可以数组也可以字符串
//                //'status'    => '',           //非必填
//                //'stage'     => '',    //非必填
//                //'nodeCode'  => '',  //非必填
//            ),
//        ];
//        $ret = $this->review->addReviewNodes($objectType, $objectID, $version, $reviewNodes);
//
//        //数据验证sql
//        /*
//        select * from zt_reviewnode zr where 1 and objectType = 'reviewtest' and objectID  =11;
//        select * from zt_reviewer zr where node in (
//           select id from zt_reviewnode zr where 1 and objectType = 'reviewtest' and objectID  =11
//        )
//        */
//        echo '插入多个审核节点成功';
//        echo '<br/>';
//
//        //新增单个审核节点（一个审核节点下可以有一个或者多个审核人）
//        $objectType = 'reviewtest1';
//        $reviewers = ['zhangsan', 'lisi', 'wangwu'];
//        $status = $this->review->getReviewNodeDefaultStatus($objectID, $objectType, $version);
//        $stage  = $this->review->getReviewDefaultStage($objectID, $objectType, $version);
//        $extParams = [];
//        $ret = $this->review->addNode($objectType, $objectID, $version, $reviewers, true, $status, $stage, $extParams);
//        //数据验证sql
//        /*
//        select * from zt_reviewnode zr where 1 and objectType = 'reviewtest1' and objectID  =11;
//        select * from zt_reviewer zr where node in (
//           select id from zt_reviewnode zr where 1 and objectType = 'reviewtest1' and objectID  =11
//        )
//        */
//        echo '插入单个审核节点成功';
        //单元格合并实例
        $data = [
            array(
                array(
                    (object)array(
                        'name' => '张三1',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三2',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三3',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三4',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三5',
                        'age' => 10,
                    ),
                ),
            ),
            array(
                array(
                    (object)array(
                        'name' => '张三6',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三7',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三8',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三9',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三10',
                        'age' => 10,
                    ),
                ),
            ),
            array(
                array(
                    (object)array(
                        'name' => '张三11',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三12',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三13',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三14',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三15',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三16',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三17',
                        'age' => 10,
                    ),
                ),
                array(
                    (object)array(
                        'name' => '张三18',
                        'age' => 10,
                    ),
                    (object)array(
                        'name' => '张三19',
                        'age' => 10,
                    ),
                ),
            ),
        ];
        $this->view->title      = $this->lang->review->review;
        $this->view->position[] = $this->lang->review->review;
        $this->view->dataList = $data;
        $this->display();

    }
}