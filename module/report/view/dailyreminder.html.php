<?php $url = $this->report->getSysURL();?>

<?php include '../../common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo date('Y-m-d') ?></td>
        <td style='width: 40px; text-align: right; background-color: #F8FAFE; border: none; vertical-align: top; padding: 10px; border-bottom: 1px solid #e5e5e5;'><?php echo html::a($url . $config->webRoot, $url . $config->webRoot, 'target="_blank"');?></td>
      </tr>
    </table>
  </td>
</tr>

<?php if(isset($mail->bugs)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->bug,  count($mail->bugs)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->bugTitle;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->deadline;?></th>
      </tr>
      <?php foreach($mail->bugs as $bug):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $bug->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('bug', 'view', "bugID=$bug->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $bug->title);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php if(!helper::isZeroDate($bug->deadline)) echo $bug->deadline;?></td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->tasks)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->task,  count($mail->tasks)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->taskName;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->deadline;?></th>
      </tr>
      <?php foreach($mail->tasks as $task):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $task->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('task', 'view', "taskID=$task->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $task->name);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php if(!helper::isZeroDate($task->deadline)) echo $task->deadline;?></td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->todos)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->todo,  count($mail->todos)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->todoName;?></th>
      </tr>
      <?php foreach($mail->todos as $todo):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $todo->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('todo', 'view', "todoID=$todo->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $todo->name);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->testTasks)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->testTask,  count($mail->testTasks)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->testTaskName;?></th>
      </tr>
      <?php foreach($mail->testTasks as $testTask):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $testTask->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('testTask', 'view', "testTask=$testTask->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $testTask->name);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->problems)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->problem,  count($mail->problems)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->problem . $this->lang->custommail->code;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->problems as $problem):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $problem->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('problem', 'view', "problemID=$problem->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $problem->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $problem->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->demands)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->demand,  count($mail->demands)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->demand . $this->lang->custommail->code;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->demands as $demand):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $demand->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('demand', 'view', "demandID=$demand->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $demand->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $demand->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->modifys)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->modify,  count($mail->modifys)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->modify . $this->lang->custommail->code;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->modifys as $modify):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $modify->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('modify', 'view', "modifyID=$modify->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $modify->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $modify->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->fixs)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->fix,  count($mail->fixs)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->fix . $this->lang->custommail->code;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->fixs as $fix):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $fix->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('info', 'view', "infoID=$fix->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $fix->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $fix->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->gains)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->gain,  count($mail->gains)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->gain . $this->lang->custommail->code;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->gains as $gain):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $gain->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('info', 'view', "infoID=$gain->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $gain->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $gain->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->plans)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->projectplan,  count($mail->plans)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->projectName;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
      </tr>
      <?php foreach($mail->plans as $plan):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $plan->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('projectplan', 'view', "planID=$plan->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $plan->mark . '_' . $plan->name);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $plan->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->reviews)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->review,  count($mail->reviews)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->reviewName;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->closingDate;?></th>
      </tr>
      <?php foreach($mail->reviews as $review):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $review->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('review', 'view', "reviewID=$review->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $review->mark . '_' . $review->title);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $review->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->changes)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->change,  count($mail->changes)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->changeCode;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->changedTime;?></th>
      </tr>
      <?php foreach($mail->changes as $change):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $change->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('change', 'view', "changeID=$change->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $change->mark . '_' . $change->code);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $change->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->requirements)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->requirement,  count($mail->requirements)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->entriesName;?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->changedTime;?></th>
      </tr>
      <?php foreach($mail->requirements as $requirement):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $requirement->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('requirement', 'view', "requirementID=$requirement->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $requirement->name);
        ?>
        </td>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
        <?php echo $requirement->lastDealDate;?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>
<?php include '../../common/view/mail.footer.html.php';?>
