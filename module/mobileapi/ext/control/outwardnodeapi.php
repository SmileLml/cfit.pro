<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * Token login.
     *
     * @param mixed $id
     */
    public function outwardNodeApi()
    {
        $id = $_POST['id'];
        $isHistory = $_POST['isHistory'];
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            $this->lang->outwarddelivery->reviewNodeList['0'] = '配置管理CM';
            $this->lang->outwarddelivery->reviewNodeList['5'] = '上海分公司领导审批';
            $this->lang->outwarddelivery->reviewNodeList['6'] = '上海分公司总经理审批';
            $this->lang->outwarddelivery->reviewNodeList['7'] = '二线专员';
        }
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'outwardNodeApi');
    }

    /**
     * 当前审核节点列表
     * @param $id
     * @return array
     */
    public function reviewNodeList($id)
    {
        list($outwardDelivery, $testingRequest, $productEnroll, $modifycncc, $TRlog, $PElog, $MClog) = $this->getView($id);
        $users = $this->loadModel('user')->getPairs('noletter');
        $nodes = $this->loadModel('review')->getNodes('outwardDelivery', $outwardDelivery->id, $outwardDelivery->version);
        if (empty($nodes)) {
            return $nodes;
        }

        if (2 == $outwardDelivery->level) {
            unset($this->lang->outwarddelivery->reviewNodeList[6]);
        } elseif (3 == $outwardDelivery->level) {
            unset($this->lang->outwarddelivery->reviewNodeList[5], $this->lang->outwarddelivery->reviewNodeList[6]);
        }

        if ($outwardDelivery->createdDate > "2024-04-02 23:59:59"){
            unset($this->lang->outwarddelivery->reviewNodeList[3]);
        }

        $data = [];
        //循环数据(内部评审节点)
        foreach ($this->lang->outwarddelivery->reviewNodeList as $key => $reviewNode) {
            $reviewerUserTitle     = '';
            $reviewerUsersShow     = '';
            $realReviewer          = new stdClass();
            $realReviewer->status  = '';
            $realReviewer->comment = '';
            if (isset($nodes[$key])) {
                $currentNode = $nodes[$key];
                $reviewers   = $currentNode->reviewers;
                if (!(is_array($reviewers) && !empty($reviewers))) {
                    continue;
                }
                //所有审核人
                $reviewersArray    = array_column($reviewers, 'reviewer');
                $reviewersArrayNew = $this->loadModel('common')->getAuthorizer(
                    'outwarddelivery',
                    implode(',', $reviewersArray),
                    $this->lang->outwarddelivery->reviewBeforeStatusList[$key],
                    $this->lang->outwarddelivery->authorizeStatusList
                );
                $reviewersArray = explode(',', $reviewersArrayNew);
                $userCount      = count($reviewersArray);
                if ($userCount > 0) {
                    $reviewerUsers     = getArrayValuesByKeys($users, $reviewersArray);
                    $reviewerUserTitle = implode(',', $reviewerUsers);
                    $subCount          = 10;
                    $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                    //获得实际审核人
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                    $extra        = json_decode($realReviewer->extra);
                    if (!empty($extra->reviewerList)) {
                        $reviewersArray    = $extra->reviewerList;
                        $reviewerUsers     = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount          = 10;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                    }
                }
            }
            if (in_array($key, $this->lang->outwarddelivery->skipNodes) && (!in_array($realReviewer->status, ['pass', 'reject']))) {
                continue;
            }

            $arr = [
                'reviewNode'     => $reviewNode,
                'reviewUser'     => $reviewerUserTitle,
                'reviewerStatus' => '',
                'comment'        => $realReviewer->comment,
                'reviewTime'     => $realReviewer->reviewTime,
                'status'         => $currentNode->status
            ];

            if ('waitsubmitted' != $outwardDelivery->status) {
                if ('ignore' == $realReviewer->status) {
                    if (3 != $key && 7 != $key) {
                        $arr['reviewerStatus'] .= '本次跳过';
                        $arr['comment'] = '已通过';
                    } elseif (3 == $key) {
                        $arr['reviewerStatus'] .= '无需处理';
                        $arr['comment'] = $reviewers[0]->comment;
                    } elseif (7 == $key) {
                        $arr['reviewerStatus'] .= '本次跳过';
                        $arr['comment'] = '';
                    }
                } else {
                    $arr['reviewerStatus'] .= zget($this->lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                }
                if (in_array($realReviewer->status, ['pass', 'reject'])) {
                    $arr['reviewUser'] = zget($users, $realReviewer->reviewer);
//                    $extra = json_decode($realReviewer->extra);
//                    $arr['reviewerStatus'] .= '(';
//                    if (!empty($extra->proxy)) {
//                        $arr['reviewerStatus'] .= zget($users, $extra->proxy, '') . '处理';
//                        $arr['reviewerStatus'] .= '【' . zget($users, $realReviewer->reviewer) . '授权】';
//                    } else {
//                        $arr['reviewerStatus'] .= zget($users, $realReviewer->reviewer, '');
//                    }
//                    $arr['reviewerStatus'] .= ')';
                }
            }
            $data[] = $arr;
        }

        $outwardDelivery->reviewFailReason = json_decode($outwardDelivery->reviewFailReason, true);
        $testFlag                          = in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing']);
//        if (!empty($outwardDelivery->reviewFailReason)) {
//            $count = count($outwardDelivery->reviewFailReason[$outwardDelivery->version]);
//            foreach ($outwardDelivery->reviewFailReason[$outwardDelivery->version] as $key => $reasons) {
//                if ($testFlag && $key == $count - 1) {
//                    continue;
//                }
//                foreach ($reasons as $k => $reason) {
//                    if (0 == $k || 1 == $k) {
//                        $data[] = [
//                            'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']],
//                            'reviewUser'     => zget($users, $reason['reviewUser'], ','),
//                            'reviewerStatus' => $reason['reviewResult'],
//                            'comment'        => $reason['reviewFailReason'],
//                            'reviewTime'     => $reason['reviewPushDate'],
//                        ];
//                    }
//                }
//            }
//        }
//
//        if ($outwardDelivery->isNewTestingRequest) {
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['0'],
//                'reviewUser'     => zget($users, 'guestjk', ','),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => '',
//            ];
//            if (in_array($testingRequest->status, ['waitqingzong', 'qingzongsynfailed'])) {
//                $arr['reviewerStatus'] = zget($this->lang->testingrequest->statusList, $testingRequest->status, '');
//            } elseif (in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'])) {
//                $arr['reviewerStatus'] = $this->lang->outwarddelivery->synSuccess;
//            }
//            if (
//                $testingRequest->pushStatus
//                && !empty($TRlog)
//                && !empty($TRlog->response)
//                && $TRlog->response->message
//                && in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing', 'qingzongsynfailed'])
//            ) {
//                $arr['comment'] = $TRlog->response->message;
//            } elseif ('qingzongsynfailed' == $testingRequest->status) {
//                $arr['comment'] = $this->lang->outwarddelivery->synFail;
//            } else {
//                $TRlog->requestDate = '';
//            }
//            if (!empty($TRlog) && !empty($TRlog->response)) {
//                $arr['reviewTime'] = $TRlog->requestDate;
//            }
//            $data[] = $arr;
//
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['1'],
//                'reviewUser'     => zget($users, 'guestcn', ','),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => strtotime($testingRequest->returnDate) > 0
//                    && in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing']) ?
//                        $testingRequest->returnDate : '',
//            ];
//            if (in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'])) {
//                $arr['reviewerStatus'] = zget($this->lang->testingrequest->statusList, $testingRequest->status);
//            }
//            if (in_array($testingRequest->status, ['withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'])) {
//                if ('testingrequestreject' == $testingRequest->status) {
//                    $arr['comment'] = '打回人：' . $testingRequest->returnPerson . '<br>' . '审批意见：' . $testingRequest->returnCase;
//                } else {
//                    $arr['comment'] = $testingRequest->returnCase;
//                }
//            }
//            $data[] = $arr;
//        }
//
//        $enrollFlag = in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass']);
//        if (!empty($outwardDelivery->reviewFailReason)) {
//            $count = count($outwardDelivery->reviewFailReason[$outwardDelivery->version]);
//            foreach ($outwardDelivery->reviewFailReason[$outwardDelivery->version] as $key => $reasons) {
//                if ($enrollFlag && $key == $count - 1) {
//                    continue;
//                }
//                foreach ($reasons as $k => $reason) {
//                    if (2 == $k || 3 == $k) {
//                        $data[] = [
//                            'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']],
//                            'reviewUser'     => zget($users, $reason['reviewUser'], ','),
//                            'reviewerStatus' => $reason['reviewResult'],
//                            'comment'        => $reason['reviewFailReason'],
//                            'reviewTime'     => $reason['reviewPushDate'],
//                        ];
//                    }
//                }
//            }
//        }
//
//        if ($outwardDelivery->isNewProductEnroll) {
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['2'],
//                'reviewUser'     => zget($users, 'guestjk', ','),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => '',
//            ];
//            if (in_array($productEnroll->status, ['waitqingzong', 'qingzongsynfailed'])) {
//                $arr['reviewerStatus'] = zget($this->lang->productenroll->statusList, $productEnroll->status, '');
//            } elseif (in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'])) {
//                $arr['reviewerStatus'] = $this->lang->outwarddelivery->synSuccess;
//            }
//            if (
//                $productEnroll->pushStatus
//                && !empty($PElog)
//                && !empty($PElog->response)
//                && $PElog->response->message
//                && in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass', 'qingzongsynfailed'])
//            ) {
//                $arr['comment'] = $PElog->response->message;
//            } elseif ('qingzongsynfailed' == $productEnroll->status) {
//                $arr['comment'] = $this->lang->outwarddelivery->synFail;
//            } else {
//                $PElog->requestDate = '';
//            }
//            if (!empty($PElog) && !empty($PElog->response)) {
//                $arr['reviewTime'] = $PElog->requestDate;
//            }
//            $data[] = $arr;
//
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['3'],
//                'reviewUser'     => zget($users, 'guestcn', ''),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => '',
//            ];
//            if (in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'])) {
//                $arr['reviewerStatus'] = zget($this->lang->productenroll->statusList, $productEnroll->status);
//            }
//            if (in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'])) {
//                if ('productenrollreject' == $productEnroll->status) {
//                    $arr['comment'] = '打回人：' . $productEnroll->returnPerson . '<br>' . '审批意见：' . $productEnroll->returnCase;
//                } else {
//                    $arr['comment'] = $productEnroll->returnCase;
//                }
//            }
//            if (
//                strtotime($productEnroll->returnDate) > 0
//                && in_array($productEnroll->status, ['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'])
//            ) {
//                $arr['reviewTime'] = $productEnroll->returnDate;
//            }
//            $data[] = $arr;
//        }
//
//        $flag = in_array($modifycncc->status, ['withexternalapproval', 'modifyfail', 'modifysuccesspart',
//            'modifysuccess', 'modifyreject', 'psdlreview', 'centrepmreview', 'giteepass', 'giteeback', ])
//            || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus));
//        if (!empty($outwardDelivery->reviewFailReason)) {
//            $count = count($outwardDelivery->reviewFailReason[$outwardDelivery->version]);
//            foreach ($outwardDelivery->reviewFailReason[$outwardDelivery->version] as $key => $reasons) {
//                if ($flag && $key == $count - 1) {
//                    continue;
//                }
//                foreach ($reasons as $k => $reason) {
//                    if (4 == $k || 5 == $k) {
//                        $data[] = [
//                            'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']],
//                            'reviewUser'     => zget($users, $reason['reviewUser'], ','),
//                            'reviewerStatus' => $reason['reviewResult'],
//                            'comment'        => $reason['reviewFailReason'],
//                            'reviewTime'     => $reason['reviewPushDate'],
//                        ];
//                    }
//                }
//            }
//        }

//        if ($outwardDelivery->isNewModifycncc) {
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['4'],
//                'reviewUser'     => zget($users, 'guestjk', ','),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => '',
//            ];
//            if (in_array($modifycncc->status, ['waitqingzong', 'qingzongsynfailed'])) {
//                $arr['reviewerStatus'] = zget($this->lang->modifycncc->statusList, $modifycncc->status, '');
//            } elseif (
//                in_array($modifycncc->status, ['withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'psdlreview', 'centrepmreview', 'giteepass', 'giteeback'])
//                || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus))
//            ) {
//                $arr['reviewerStatus'] = $this->lang->outwarddelivery->synSuccess;
//            }
//            if (
//                $modifycncc->pushStatus
//                && !empty($MClog)
//                && !empty($MClog->response)
//                && $MClog->response->message
//                && (in_array($modifycncc->status, [
//                    'withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'psdlreview',
//                    'centrepmreview', 'giteepass', 'giteeback', 'qingzongsynfailed', ])
//                    || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus))
//                )
//            ) {
//                $arr['comment'] = $MClog->response->message;
//            } elseif ('qingzongsynfailed' == $modifycncc->status) {
//                $arr['comment'] = $this->lang->outwarddelivery->synFail;
//            } else {
//                $MClog->requestDate = '';
//            }
//            if (!empty($MClog) && !empty($MClog->response)) {
//                $arr['reviewTime'] = $MClog->requestDate;
//            }
//            $data[] = $arr;
//
//            $arr = [
//                'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList['5'],
//                'reviewUser'     => zget($users, 'guestcn', ''),
//                'reviewerStatus' => '',
//                'comment'        => '',
//                'reviewTime'     => '',
//            ];
//            if (
//                in_array($modifycncc->status, ['withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess',
//                    'modifyreject', 'psdlreview', 'centrepmreview', 'giteepass', 'giteeback', ])
//                || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus))) {
//                $arr['reviewerStatus'] = zget($this->lang->modifycncc->statusList, $modifycncc->status);
//                if ('modifyreject' == $modifycncc->status) {
//                    $arr['reviewerStatus'] .= '（金信退回总中心，仅供参考）';
//                }
//            }
//            if (
//                in_array($modifycncc->status, ['withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess',
//                    'modifyreject', 'psdlreview', 'centrepmreview', 'giteepass', 'giteeback', ])
//                || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus))
//            ) {
//                if ('giteeback' == $modifycncc->status) {
//                    $arr['comment'] = '打回人：' . $modifycncc->approverName . '<br>审批意见：' . $modifycncc->reasonCNCC;
//                } else {
//                    $arr['comment'] = $modifycncc->reasonCNCC;
//                }
//            }
//            if (
//                strtotime($modifycncc->feedbackDate) > 0
//                && (in_array($modifycncc->status, ['withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess',
//                    'modifyreject', 'psdlreview', 'centrepmreview', 'giteepass', 'giteeback', ])
//                    || ('modifycancel' == $modifycncc->status && !empty($modifycncc->changeStatus))
//                )
//            ) {
//                $arr['reviewTime'] = $modifycncc->feedbackDate;
//            }
//            $data[] = $arr;
//        }

        return $data;
    }

    public function historyReviewNode($id)
    {
        $this->loadModel('outwarddelivery');
        $this->app->loadLang('outwarddelivery');

        $outwarddelivery           = $this->outwarddelivery->getByID($id);
        $res              = $this->dao
            ->select('version')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('outwarddelivery')
            ->andWhere('objectID')->eq($id)
            ->groupby('version')
            ->fetchall();
        $versions         = array_column($res, 'version');
        $users            = $this->loadModel('user')->getPairs('noletter');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('outwarddelivery', $id, $version);
            foreach ($data as $k => $v) {
                if ('wait' == $v->status || !(is_array($v->reviewers) && !empty($v->reviewers))) {
                    unset($data[$k]);
                }
                // 后续不再显示系统部审核节点，去掉
                if ($v->stage == 4 && isset($modify->createdDate) && $modify->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }

        $data = [];
        foreach ($nodes as $version => $node){
            $nodes = (array)$node['nodes'];
            if ($outwarddelivery->level == 2){
                unset($this->lang->outwarddelivery->reviewNodeList[6]);
            }elseif ($outwarddelivery->level == 3){
                unset($this->lang->outwarddelivery->reviewNodeList[5]);
                unset($this->lang->outwarddelivery->reviewNodeList[6]);
            }
            if ($outwarddelivery->createdDate > "2024-04-02 23:59:59"){
                unset($this->lang->outwarddelivery->reviewNodeList[3]);
            }
            $arrs = [];
            foreach ($this->lang->outwarddelivery->reviewNodeList as $key => $reviewNode){
                $reviewerUserTitle = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';

                if(!isset($nodes[$key])){
                    continue;
                }

                $currentNode = $nodes[$key];
                $reviewers = $currentNode->reviewers;
                if (!(is_array($reviewers) && !empty($reviewers))) {
                    continue;
                }
                //所有审核人
                $reviewersArray = array_column($reviewers, 'reviewer');
                $reviewersArrayNew = $this->loadModel('common')->getAuthorizer(
                    'outwarddelivery',
                    implode(',', $reviewersArray),
                    $this->lang->outwarddelivery->reviewBeforeStatusList[$key],
                    $this->lang->outwarddelivery->authorizeStatusList);
                $reviewersArray = explode(',', $reviewersArrayNew);
                $userCount = count($reviewersArray);
                if ($userCount > 0) {
                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                    $reviewerUserTitle = implode(',', $reviewerUsers);
                    $subCount = 10;
                    //获得实际审核人
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                }

                $arr = [
                    'reviewNode'     => $reviewNode,
                    'reviewUser'     => $reviewerUserTitle,
                    'reviewerStatus' => '',
                    'comment'        => $realReviewer->comment,
                    'reviewTime'     => $realReviewer->reviewTime,
                    'status'         => $currentNode->status
                ];

                if ($outwarddelivery->status != ''){
                    if($realReviewer->status == 'ignore'){
                        if($key != 3 and $key != 7){
                            $arr['reviewerStatus'] = '本次跳过';
                            $arr['comment'] = '已通过';
                        }else if($key == 3){
                            $arr['reviewerStatus'] = '无需处理';
                            $arr['comment'] = $reviewers[0]->comment;
                        }else if($key == 7){
                            $arr['reviewerStatus'] = '';
                            $arr['comment'] = '';
                        }
                    }else{
                        $arr['reviewerStatus'] = zget($this->lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                    }
                    if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                        $arr['reviewUser'] = zget($users, $realReviewer->reviewer);

//                        $extra = json_decode($realReviewer->extra);
//                        if(!empty($extra->proxy)){
//                            $arr['reviewerStatus'] .= '(' . zget($users, $extra->proxy, '');
//                            $arr['reviewerStatus'] .= "【".zget($users, $realReviewer->reviewer)."授权处理】)";
//                        }else{
//                            $arr['reviewerStatus'] .= '(' . zget($users, $realReviewer->reviewer, '') . ')';
//                        }
                    }
                }
                $arrs[] = $arr;
            }
            $data[$version] = $arrs;
        }
//        if(!empty($outwarddelivery->reviewFailReason)){
//            $reviewFailReason = json_decode($outwarddelivery->reviewFailReason, true);
//            foreach ($reviewFailReason as $version => $item){
//                if(count($item) !== count($item, COUNT_RECURSIVET)){
//                    foreach ($item as $reason){
//                        $data[$version][] = [
//                            'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']],
//                            'reviewUser'     => zget($users, $reason['reviewUser'], ','),
//                            'reviewerStatus' => $reason['reviewResult'],
//                            'comment'        => $reason['reviewFailReason'],
//                            'reviewTime'     => $reason['reviewPushDate'],
//                        ];
//                    }
//                }else{
//                    $data[$version][] = [
//                        'reviewNode'     => $this->lang->outwarddelivery->outerReviewNodeList[$item['reviewNode']],
//                        'reviewUser'     => zget($users, $item['reviewUser'], ','),
//                        'reviewerStatus' => $item['reviewResult'],
//                        'comment'        => $item['reviewFailReason'],
//                        'reviewTime'     => $item['reviewPushDate'],
//                    ];
//                }
//            }
//        }
        return $data;
    }

    public function getView($outwarddeliveryID)
    {
        $this->loadModel('outwarddelivery');
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->app->loadLang('defect');

        $allInfo         = $this->outwarddelivery->getAllInfo($outwarddeliveryID);
        $outwarddelivery = (object)$allInfo['outwardDelivery'];
        $testingrequest  = $allInfo['testingRequest'];
        $productenroll   = $allInfo['productEnroll'];
        $modifycncc      = $allInfo['modifycncc'];
        $relations       = $this->outwarddelivery->getAllRelations($outwarddeliveryID);

        if ($outwarddelivery->isNewTestingRequest) {
            $TRlog = $this->loadModel('testingrequest')->getRequestLog($outwarddelivery->testingRequestId);
            if (empty($TRlog)) {
                $TRlog = new stdClass();
            }
        }
        if ($outwarddelivery->isNewProductEnroll) {
            $PElog = $this->loadModel('productenroll')->getRequestLog($outwarddelivery->productEnrollId);
            if (empty($PElog)) {
                $PElog = new stdClass();
            }
        }
        if ($outwarddelivery->isNewModifycncc) {
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            $MClog      = $this->loadModel('modifycncc')->getRequestLog($outwarddelivery->modifycnccId);
            if (empty($MClog)) {
                $MClog = new stdClass();
            }
            if (!empty($modifycncc->returnLog)) {
                $modifycncc->returnLogArray = json_decode($modifycncc->returnLog);
                $verificationReturnNum      = 0;
                foreach ($modifycncc->returnLogArray as $key => $value) {
                    if ('基准审核中' == $value->node || '基准实验室审核' == $value->node) {
                        ++$verificationReturnNum;
                    }
                }
                $modifycncc->verificationReturnNum = $verificationReturnNum;
            }
            $modifycnccList = $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,25)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
            $modifycncc     = $modifycncc;
        }
        $nodes                      = $this->loadModel('review')->getNodes('outwardDelivery', $outwarddeliveryID, $outwarddelivery->version);
        $outwarddelivery->reviewers = $this->loadModel('review')->getReviewer('outwardDelivery', $outwarddeliveryID, $outwarddelivery->version, $outwarddelivery->reviewStage);

        //授权管理转化
        $outwarddelivery->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $outwarddelivery->dealUser, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);

        return [$outwarddelivery, $testingrequest, $productenroll, $modifycncc, $TRlog, $PElog, $MClog];
    }
}
