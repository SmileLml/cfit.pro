<?php
include '../../control.php';
class myHistory extends history
{
    public function editByReviewToDeal()
    {
//        $res = $this->problem();
//        echo '问题池修改了' . count($res) . '条数据' . '<br/>';
//        a($res);
//
//        $res = $this->modify();
//        echo '金信对外交付修改了' . count($res) . '条数据' . '<br/>';
//        a($res);
//
//        $res = $this->info();
//        echo '金信数据获取修改了' . count($res) . '条数据' . '<br/>';
//        a($res);
//
//        $res = $this->outWardDelivery();
//        echo '清总对外交付、测试申请、产品登记修改了' . count($res) . '条数据' . '<br/>';
//        a($res);
//
//        $res = $this->infoQz();
//        echo '清总数据获取修改了' . count($res) . '条数据' . '<br/>';
//        a($res);

        $res = $this->sectransfer();
        echo '对外移交修改了' . count($res) . '条数据' . '<br/>';
        a($res);

//        $res = $this->requirement();
//        echo '需求任务修改了' . count($res) . '条数据' . '<br/>';
//        a($res);

    }

    public function problem()
    {
        //查询问题单
        $items = $this->dao
            ->select('id')
            ->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->fetchAll();
        //循环问题单
        $ids = [];
        foreach ($items as $item){
            //循环中查询产创处理人员
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->eq('deptapproved')
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }
            //根据问题类型和问题单ID和人员名称和操作动作查询历史记录表
            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('review')
                ->fetchAll('id');
            if(empty($info)) continue;

            //审批动作修改为处理动作
            $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;

    }

    public function requirement()
    {
        //查询问题单
        $items = $this->dao
            ->select('id')
            ->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->fetchAll();

        $ids = [];
        foreach ($items as $item){
            //循环中查询产创处理人员
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('requirement')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->eq('toinnovateapproved')
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }
            //根据问题类型和问题单ID和人员名称和操作动作查询历史记录表
            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('requirement')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('reviewed')
                ->fetchAll('id');
            if(empty($info)) continue;

            //审批动作修改为处理动作
            $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;

    }

    public function modify()
    {
        $items = $this->dao->select('id')->from(TABLE_MODIFY)->where('status')->ne('deleted')->fetchAll();
        $ids   = [];
        foreach ($items as $item){
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('modify')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->in(['gmsuccess','cmconfirmed'])
                ->andWhere('deleted')->ne(1)
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }
            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('modify')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('review')
                ->fetchAll('id');
            if(empty($info)) continue;

            $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;
    }

    public function info()
    {
        $items = $this->dao->select('id')->from(TABLE_INFO)->where('status')->ne('deleted')->fetchAll();
        $ids   = [];
        foreach ($items as $item){
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('info')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->in(['gmsuccess','cmconfirmed'])
                ->andWhere('deleted')->ne(1)
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }

            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('info')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('review')
                ->fetchAll('id');
            if(empty($info)) continue;

            $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;
    }

    public function outWardDelivery(): array
    {
        $items = $this->dao->select('id,productEnrollId,testingRequestId')->from(TABLE_OUTWARDDELIVERY)->where('status')->ne('deleted')->andWhere('deleted')->ne(1)->fetchAll();
        $ids   = [];
        foreach ($items as $item){
            $arr = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->in(['gmsuccess','cmconfirmed'])
                ->andWhere('deleted')->ne(1)
                ->fetchAll('id');
            if(empty($arr)) continue;

            $date     = [];
            $accounts = [];
            foreach ($arr as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }

            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('action')->in('review')
                ->andWhere('date')->in($date)
                ->fetchAll('id');
            if(!empty($info)) {
                $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();
                $ids['outwarddelivery'][] = $item->id;
            }

            if($item->testingRequestId){
                $info = $this->dao
                    ->select('id')
                    ->from(TABLE_ACTION)
                    ->where('objectType')->eq('testingrequest')
                    ->andWhere('objectID')->eq($item->testingRequestId)
                    ->andWhere('actor')->in($accounts)
                    ->andWhere('date')->in($date)
                    ->andWhere('action')->eq('review')
                    ->fetchAll('id');
                if(!empty($info)) {
                    $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();
                    $ids['testingrequest'][] = $item->testingRequestId;
                }
            }

            if($item->productEnrollId){
                $info = $this->dao
                    ->select('id')
                    ->from(TABLE_ACTION)
                    ->where('objectType')->eq('productEnroll')
                    ->andWhere('objectID')->eq($item->productEnrollId)
                    ->andWhere('actor')->in($accounts)
                    ->andWhere('date')->in($date)
                    ->andWhere('action')->eq('review')
                    ->fetchAll('id');
                if(!empty($info)) {
                    $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();
                    $ids['productEnroll'][] = $item->productEnrollId;
                }
            }
        }

        return $ids;
    }

    public function infoQz(): array
    {
        $items = $this->dao->select('id')->from(TABLE_INFO_QZ)->where('status')->ne('deleted')->fetchAll();
        $ids   = [];
        foreach ($items as $item){
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('infoqz')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->in(['leadersuccess','cmconfirmed'])
                ->andWhere('deleted')->ne(1)
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }

            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('infoqz')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('review')
                ->fetchAll('id');
            if(empty($info)) continue;

            $this->dao->update(TABLE_ACTION)->set('action')->eq('deal')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;
    }

    public function sectransfer(): array
    {
        $items = $this->dao->select('id')
            ->from(TABLE_SECTRANSFER)
            ->where('status')->ne('deleted')
            ->andWhere('deleted')->ne(1)
            ->fetchAll();
        $ids   = [];
        foreach ($items as $item){
            $info = $this->dao
                ->select('id, account, createdDate')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('sectransfer')
                ->andWhere('objectId')->eq($item->id)
                ->andWhere('`before`')->in(['waitCMApprove','waitSecApprove'])
                ->andWhere('deleted')->ne(1)
                ->fetchAll('id');
            if(empty($info)) continue;

            $date     = [];
            $accounts = [];
            foreach ($info as $val){
                $date[] = $val->createdDate;
                $date[] = date('Y-m-d H:i:s', strtotime($val->createdDate) + 1);

                $accounts[$val->account] = $val->account;
            }

            $info = $this->dao
                ->select('id')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('sectransfer')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('actor')->in($accounts)
                ->andWhere('date')->in($date)
                ->andWhere('action')->eq('reviewed')
                ->fetchAll('id');
            if(empty($info)) continue;

            $this->dao->update(TABLE_ACTION)->set('action')->eq('dealed')->where('id')->in(array_keys($info))->exec();

            $ids[] = $item->id;
        }

        return $ids;
    }
}