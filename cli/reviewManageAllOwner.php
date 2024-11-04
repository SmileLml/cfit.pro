<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class reviewManageAllOwner extends control
{
    //更新review_meeting 和review表 allOwner字段
    /**
     * allowner 包含 : 评审主席、预计参会专家、评审专员、各评审的新建者、项目经理、发起部门的领导、质量部人员、相关人员、质量部CM
     * @return string
     */
    public function updateAllOwner()
    {

        $this->config->debug = 2; //启动报错
        saveLog('执行：updateAllOwner','reviewManageAllOwner');
        //$dept = $this->app->user->dept;
        //allowner 包含 : 评审主席、预计参会专家、评审专员、各评审的新建者、项目经理、发起部门的领导、质量部人员、相关人员、质量部CM
        try {
            //会议表      发起部门的领导 质量部人员
            $meet = $this->dao->select('t1.id,t1.owner,t1.reviewer,t2.review_meeting_id,t2.review_id,t3.meetingPlanExport,t3.createdBy,t3.relatedUsers,t3.qualityCm,t7.PM,t5.manager')->from(TABLE_REVIEW_MEETING)->alias('t1')
                ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
                ->on('t1.id = t2.review_meeting_id')
                ->leftJoin(TABLE_REVIEW)->alias('t3')
                ->on('t2.review_id =  t3.id')
                ->leftJoin(TABLE_PROJECT)->alias('t4')
                ->on('t3.project =  t4.id')
                ->leftJoin(TABLE_DEPT)->alias('t5')
                ->on('t3.createdDept =  t5.id')
                ->leftJoin(TABLE_PROJECTPLAN)->alias('t6')
                ->on('t4.id =  t6.project')
                ->leftJoin(TABLE_PROJECTCREATION)->alias('t7')
                ->on('t6.id = t7.plan')
                ->where('t1.deleted')->eq(0)
                ->fetchAll();
            $events = array();
            foreach($meet as $item){
                $meetid = $item->review_meeting_id;
                $event = array();
                $event['review_meeting_id'] = $meetid;
                $event['owner'] = explode(',',$item->owner);
                $event['reviewer'] = explode(',',$item->reviewer);

                $list = array();
                $list['review_id'] = $item->review_id;
                $list['meetingPlanExport'] = explode(',',$item->meetingPlanExport);
                $list['createdBy'] = explode(',',$item->createdBy);
                $list['relatedUsers'] = explode(',',$item->relatedUsers);
                $list['qualityCm'] = explode(',',$item->qualityCm);
                $list['PM'] = explode(',',$item->PM);
                $list['manager'] = explode(',',$item->manager);

                if(isset($events[$meetid])){
                    $events[$meetid]['list'][] = (array)$list;
                }else{
                    $events[$meetid] = $event;
                    $events[$meetid]['list'][] = $list;
                }
            }
            //质量部人员
            $name = $this->dao->select('account')->from(TABLE_USER)->where('deleted')->eq('0')->andWhere('dept')->eq('3')->fetchAll();
            $name = implode(',',array_column($name,'account'));
            foreach ($events as $key=>$value) {

                $review_id = array_column($value['list'],'review_id');

                $meetingPlanExport = array_column($value['list'],'meetingPlanExport');
                $createdBy    = array_column($value['list'],'createdBy');
                $relatedUsers = array_column($value['list'],'relatedUsers');
                $qualityCm    = array_column($value['list'],'qualityCm');
                $PM           = array_column($value['list'],'PM');
                $manager      = array_column($value['list'],'manager');

                $all          = array_merge($value['owner'],$value['reviewer'],$meetingPlanExport,$createdBy,$relatedUsers,$qualityCm,$PM,$manager);
                $all = $this->arrayToString($all);
                $all = $all .','.$name;
                $all = implode(',',array_unique(array_filter(explode(',',$all))));
                $this->dao->update(TABLE_REVIEW_MEETING)->set('allOwner')->eq("$all")->where('id')->eq($value['review_meeting_id'])->exec();
                $this->dao->update(TABLE_REVIEW)->set('allOwner')->eq("$all")->where('id')->in($review_id)->exec();

            }

        } catch (Exception $e) {
            $res['allmeet'] = $e;
        }
        return $events;
    }

    /**
     * 未排会议
     * @return mixed
     */
    public function updateNoAllOwner()
    {
        $this->config->debug = 2; //启动报错
        saveLog('执行：updateNoAllOwner', 'reviewManageAllOwner');
        //allowner 包含 : 评审主席、预计参会专家、评审专员、各评审的新建者、项目经理、发起部门的领导、质量部人员、相关人员、质量部CM
        try {
            $nomeets = $this->dao->select('t3.id,t3.owner,t3.reviewer,t3.meetingPlanExport,t3.createdBy,t3.relatedUsers,t3.qualityCm,t4.PM,t5.manager')->from(TABLE_REVIEW)->alias('t3')
                ->leftJoin(TABLE_PROJECT)->alias('t4')
                ->on('t3.project =  t4.id')
                ->leftJoin(TABLE_DEPT)->alias('t5')
                ->on('t3.createdDept =  t5.id')
                ->where('t3.deleted')->eq(0)
                ->andWhere('t3.type')->ne('cbp')
                ->andWhere('t3.grade')->eq("meeting")->andWhere('t3.meetingPlanTime')->eq("0000-00-00 00:00:00")
                ->orderBy('id desc')
                ->fetchAll('id');
            //质量部人员
            $name = $this->dao->select('account')->from(TABLE_USER)->where('deleted')->eq('0')->andWhere('dept')->eq('3')->fetchAll();
            $name = implode(',',array_column($name,'account'));

            foreach($nomeets as $item){
                $id = $item->id;
                $owner = explode(',',$item->owner);
                $reviewer = explode(',',$item->reviewer);
                $meetingPlanExport = explode(',',$item->meetingPlanExport);
                $createdBy = explode(',',$item->createdBy);
                $relatedUsers = explode(',',$item->relatedUsers);
                $qualityCm = explode(',',$item->qualityCm);
                $PM = explode(',',$item->PM);
                $manager = explode(',',$item->manager);

                $all          = array_merge($owner,$reviewer,$meetingPlanExport,$createdBy,$relatedUsers,$qualityCm,$PM,$manager);
                $all = $this->arrayToString($all);
                $all = $all .','.$name;
                $all = implode(',',array_unique(array_filter(explode(',',$all))));
                $this->dao->update(TABLE_REVIEW)->set('allOwner')->eq("$all")->where('id')->in($id)->exec();
            }

        } catch (Exception $e) {
            $res['nomeet'] = $e;
        }
        return $nomeets;
    }

    /**
     * 数组转字符串
     */
    public function arrayToString($arr){
        $str = "";
        foreach ($arr as $item) {
            if(is_string($item)){
                $str .= $item.',';
            }else{
                $str .= implode(',',$item).',';
            }
        }
        return $str;
    }
}

//$lock = getLock('reviewManageAllOwner', 40); //锁定防止重复
$lock = getTimeLock('reviewManageAllOwner', 40); //锁定防止重复
$reviewManageAllOwner = new reviewManageAllOwner();
$data = $reviewManageAllOwner->updateAllOwner();
saveLog($data, 'reviewManageAllOwner');
$nodata = $reviewManageAllOwner->updateNoAllOwner();
saveLog($nodata, 'reviewManageAllOwner');
unlock($lock); //解除锁定
