<?php

include '../../control.php';
class myProblem extends problem
{
    public function redeal($id)
    {
        $problem = $this->loadModel('problem')->getByID($id, true);
        //验证权限（问题单是否关联制版）
        $userList = array_merge([$problem->acceptUser], $this->lang->problem->redealUserList);
        $flag     = 'feedbacked' == $problem->status && in_array($_SESSION['user']->account, $userList);
        if (!$flag) {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID={$id}");
            $this->send($response);
        } else {
            $ids    = "select max(id) from zt_build where project = t.project and app = t.app and product = t.product and  problemid like '%{$problem->code}%' and  deleted = '0' group by taskid";
            $builds = $this->dao->select('t.id,t.`name`,t.`status`,t.dealuser')
                ->from(TABLE_BUILD)->alias('t')
                ->where('t.problemid')->like("%{$problem->code}%")
                ->andwhere('t.`status`')->ne('wait')
                ->andWhere("id in({$ids})")
                ->andwhere('t.deleted')->eq(0)
                ->fetchAll('id');
            if ($builds) {
                $name = '【'.implode('，', array_column((array)$builds, 'name')).'】';
                $this->view->res = ['result' => 'fail', 'message' => $this->lang->problem->buildsError . $name];
                $this->display();
                return false;
            }
        }

        if ($_POST) {
            $data = fixer::input('post')
                ->join('product', ',')
                ->join('productPlan', ',')
                ->remove('uid,app')
                ->stripTags($this->config->problem->editor->redeal['id'], $this->config->allowedTags)
                ->get();

            //判断所属产品和所属产品版本是否为空
            if (empty($data->product)) {
                $response['result']  = 'fail';
                $response['message'] = ['product' => $this->lang->problem->productEmpty];
                $this->send($response);
            }
            if (empty($data->productPlan)) {
                $response['result']  = 'fail';
                $response['message'] = ['productPlan' => $this->lang->problem->productAndPlanEmpty];
                $this->send($response);
            }
            $flag = $this->loadModel('problem')->checkProductAndPlanOnly($this->post->product, $this->post->productPlan);
            if ($flag == 'fail') {
                $response['result']  = 'fail';
                $response['message'] = ['' => $this->lang->problem->productOnly];
                $this->send($response);
            } else if ($flag == 'no') {
                $response['result']  = 'fail';
                $response['message'] = ['' => $this->lang->problem->productAndPlanEmpty];
                $this->send($response);
            } else if ($flag == 'wu') {
                $response['result']  = 'fail';
                $response['message'] = ['' => $this->lang->problem->wuError];
                $this->send($response);
            }
            //判断下一节点处理人是否存在
            if (empty($data->dealUser)){
                $response['result']  = 'fail';
                $response['message'] = ['dealUser' => $this->lang->problem->nextUserEmpty];
                $this->send($response);
            }

            //当前进展追加
            $data->progress = trim(str_replace('&nbsp;', '', $data->progress));
            if ($data->progress) {
                $users    = $this->loadModel('user')->getPairs('noclosed');
                $progress = '<span style="background-color: #ffe9c6">' . helper::now() . ' 由<strong>' . zget($users, $this->app->user->account, '') . '</strong>新增' . '<br></span>' . $data->progress;
                $data->progress = $problem->progress . '<br>' . $progress;
            } else {
                $response['result']  = 'fail';
                $response['message'] = ['progress' => $this->lang->problem->progressEmpty];
                $this->send($response);
            }

            $res = $this->dao->update(TABLE_PROBLEM)
                ->set('product')->eq($data->product)
                ->set('productPlan')->eq($data->productPlan)
                ->set('progress')->eq($data->progress)
                ->where('id')->eq($id)->exec();

            //重新生成任务
            foreach ($problem as $key => $value) {
                if (!isset($data->{$key})) {
                    $data->{$key} = $value;
                }
            }
            $this->loadModel('problem')->createProblemManyTask($data, $problem, $id);

            //工作进展同步给外部
            $flag = $this->lang->problem->OverDateList;
            if ($flag['openType'] && $problem->IssueId && $problem->progress != $data->progress) {
                $pushEnable = $this->config->global->pushProblemCommentEnable;
                if ('enable' == $pushEnable) {
                    $url           = $this->config->global->pushProblemCommentUrl;
                    $pushAppId     = $this->config->global->pushProblemCommentAppId;
                    $pushAppSecret = $this->config->global->pushProblemCommentAppSecret;
                    $headers       = ['App-Id: ' . $pushAppId, 'App-Secret: ' . $pushAppSecret];
                    $pushData      = [
                        'IssueId'            => $problem->IssueId,
                        'problemJinKeStatus' => html_entity_decode(strip_tags(str_replace('<br />', "\n", (string)(
                            htmlspecialchars_decode($data->progress, ENT_QUOTES)
                        )))),
                    ];
                    $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', [], $headers);
                    $status = 'fail';
                    if (!empty($result)) {
                        $resultData = json_decode($result);
                        if ('200' == $resultData->code || 1 == $resultData->isSave) { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                            $status = 'success';
                        }
                        $response = $result;
                    } else {
                        $response = '对方无响应';
                    }
                    $this->requestlog->saveRequestLog($url, 'problem', 'pushProblemComment', 'POST', $pushData, $response, $status, '', $id);
                }
            }
            $response = [];
            $this->loadModel('action')->create('problem', $id, 'redeal', '', $this->app->user->account);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->details     = $this->problem->getProductAndPlan($problem->product, $problem->productPlan);
        $this->view->productList = $problem->app ? ['0' => '', '99999' => '无'] + $this->loadModel('product')->getCodeNamePairsByApp($problem->app) : ['0' => '', '99999' => '无'];
        $this->view->problem     = $problem;
        $this->display();
    }
}
