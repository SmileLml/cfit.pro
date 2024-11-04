<?php

include '../../control.php';
class myProblem extends problem
{
    /**
     * 修改反馈期限
     * @param mixed $problemID
     */
    public function feedbackTimeEdit($problemID)
    {
        if ($_POST) {
            $data = fixer::input('post')
                ->remove('uid')
                ->join('isChangeFeedbackTime', ',')
                ->stripTags($this->config->problem->editor->updatefeedbacktime['id'], $this->config->allowedTags)
                ->get();
            if (!isset($data->isChangeFeedbackTime)) {
                $data->isChangeFeedbackTime = 0;
            }

            $problem = $this->problem->getByID($problemID);
            if(!empty($data->feedbackStartTimeInside) && $data->feedbackStartTimeInside != '0000-00-00 00:00:00'){
                if($problem->createdBy == 'guestcn'){
                    $data->feedbackEndTimeInside = helper::getWorkDay($data->feedbackStartTimeInside, $this->lang->problem->expireDaysList['days']) . substr($data->feedbackStartTimeInside, 10);
                }elseif ($problem->createdBy == 'guestjx'){
                    $data->feedbackEndTimeInside = date('Y-m-d H:i:s',strtotime("+".$this->lang->problem->expireDaysList['jxExpireDays'].' day',strtotime($data->feedbackStartTimeInside))) ;
                }
            }
            if(!empty($data->feedbackStartTimeOutside) && $data->feedbackStartTimeOutside != '0000-00-00 00:00:00'){
                if($problem->createdBy == 'guestcn'){
                    $data->feedbackEndTimeOutside = helper::getWorkDay($data->feedbackStartTimeOutside, $this->lang->problem->expireDaysList['days']) . substr($data->feedbackStartTimeOutside, 10);
                }elseif ($problem->createdBy == 'guestjx'){
                    $data->feedbackEndTimeOutside = date('Y-m-d H:i:s',strtotime("+".$this->lang->problem->expireDaysList['jxExpireDays'].' day',strtotime($data->feedbackStartTimeOutside))) ;
                }
            }

            $this->dao->update(TABLE_PROBLEM)->data($data)
                ->autoCheck()
                ->where('id')->eq($problemID)
                ->exec();

            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }else {
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
            }

            $this->send($response);
        }

        $problem = $this->problem->getByID($problemID);
        $problem->feedbackStartTimeInside  = $problem->ifOverDateInside['start'] ?? '';
        $problem->feedbackEndTimeInside    = $problem->ifOverDateInside['end'] ?? '';
        $problem->feedbackStartTimeOutside = $problem->ifOverDate['start'] ?? '';
        $problem->feedbackEndTimeOutside   = $problem->ifOverDate['end'] ?? '';

        $this->view->problem = $problem;
        $this->display();
    }
}
