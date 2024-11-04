<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <tr>
            <th><?php echo $lang->secondorder->sourceBackground;?></th>
            <td><?php
                if($secondorder->formType == 'external'){
                    echo html::select(
                            'sourceBackground',
                            $lang->secondorder->sourceBackgroundList,
                            $secondorder->sourceBackground,
                            "class='form-control chosen' onchange=sourceBackgroundChange(this.value) readonly='true'"
                    );
                }else{
                    echo html::select(
                        'sourceBackground',
                        $lang->secondorder->sourceBackgroundList,
                        $secondorder->sourceBackground,
                        "class='form-control chosen' onchange=sourceBackgroundChange(this.value)"
                    );
                }
                ?></td>
            <td>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->secondorder->taskIdentification; ?></span>
                    <?php echo html::select('taskIdentification', ['' => ''] + $lang->secondorder->taskIdentificationList, $secondorder->taskIdentification, "class='form-control chosen'");?>
                </div>
            </td>
        </tr>
          <tr>
              <th><?php echo $lang->secondorder->summary;?></th>
              <td colspan='2'><?php
                  if($secondorder->formType == 'external'){
                      echo html::input('summary', $secondorder->summary, "class='form-control' maxlength='200' readonly='true'");
                  }else{
                      echo html::input('summary', $secondorder->summary, "class='form-control' maxlength='200'");
                  }
                  ?></td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->type;?></th>
              <td><?php
                  if($secondorder->formType == 'external'){
                      echo html::select('type', $lang->secondorder->typeList, $secondorder->type, "class='form-control chosen' readonly='true'");
                  }else{
                      $typeList = array_diff($lang->secondorder->typeList, array_filter($lang->secondorder->delTypeList));
                      $lang->secondorder->typeList = $typeList + [$secondorder->type => $lang->secondorder->typeList[$secondorder->type]];
                      echo html::select('type', $lang->secondorder->typeList, $secondorder->type, "class='form-control chosen'");
                  }
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->subtype; ?></span>
                      <?php
                      if($secondorder->formType == 'external'){
                          echo html::select('subtype', $childTypeList, $secondorder->subtype, "class='form-control chosen' readonly='true'");
                      }else{
                          echo html::select('subtype', $childTypeList, $secondorder->subtype, "class='form-control chosen'");
                      }
                      ?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->source;?></th>
              <td><?php
                  if($secondorder->frommTyep == 'internal'){
                      unset($lang->secondorder->sourceList['qz']);
                      unset($lang->secondorder->sourceList['jx']);
                  }
                  echo html::select('source', $lang->secondorder->sourceList, $secondorder->source, "class='form-control chosen'");
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->exceptDoneDate; ?></span>
                      <?php
                      if($secondorder->formType == 'external'){
                          echo html::input('exceptDoneDate', $secondorder->exceptDoneDate, "class='form-control form-date' readonly='true'");
                      }else{
                          echo html::input('exceptDoneDate', $secondorder->exceptDoneDate, "class='form-control form-date' ");
                      }
                      ?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->app;?></th>
              <td><?php
                  if($secondorder->formType == 'external'){
                      echo html::select('app', $apps, $secondorder->app, "class='form-control chosen' readonly='true'");
                  }else{
                      echo html::select('app', $apps, $secondorder->app, "class='form-control chosen'");
                  }
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->cbpProject; ?></span>
                      <?php
                      if($secondorder->formType == 'external'){
                          echo html::select('cbpProject', $outsideplan, $secondorder->cbpProject, "class='form-control chosen' readonly='true' required");
                      }else{
                          echo html::select('cbpProject', $outsideplan, $secondorder->cbpProject, "class='form-control chosen' required");
                      }
                      ?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->union;?></th>
              <td><?php echo html::select('union', $this->lang->opinion->unionList, $secondorder->union, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->team; ?></span>
                      <?php echo html::select('team', $this->lang->application->teamList, $secondorder->team, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
        <tr>
            <th><?php echo $lang->secondorder->contacts;?></th>
            <td><?php
                if($secondorder->formType == 'external'){
                    echo html::input('contacts', zget($users, $secondorder->contacts, $secondorder->contacts), "class='form-control' readonly='true' maxlength='200'");
                }else{
                    echo html::input('contacts', zget($users, $secondorder->contacts, $secondorder->contacts), "class='form-control' maxlength='200'");
                }
                ?></td>
            <td>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->secondorder->contactsPhone; ?></span>
                    <?php
                    if($secondorder->formType == 'external'){
                        echo html::input('contactsPhone', $secondorder->contactsPhone, "class='form-control' minlength='11' maxlength='11' readonly='true'");
                    }else{
                        echo html::input('contactsPhone', $secondorder->contactsPhone, "class='form-control' minlength='11' maxlength='11'");
                    }
                    ?>
                </div>
            </td>
        </tr>
          <tr>
              <th><?php echo $lang->secondorder->PO;?></th>
              <td><?php
                  if(isset($lang->secondorder->secondUserList[$app->user->account])){
                      echo html::select('dealUser', $users, $secondorder->acceptUser, "class='form-control chosen'");
                  }elseif ($secondorder->status == 'backed'){
                      echo html::select('dealUser', $users, $secondorder->acceptUser, "class='form-control chosen'");
                  }else{
                      $executive = array_intersect_key($users, array_flip(explode(',', $executive)));
                      echo html::select('dealUser', array('' => '') + $executive, $secondorder->acceptUser, "class='form-control chosen'");
                  }
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->cc; ?></span>
                      <?php echo html::select('ccList[]', $users, $secondorder->ccList, "class='form-control chosen'multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->filelist;?></th>
              <td>
                  <div class='detail'>
                      <div class='detail-content article-content'>
                          <?php
                          if($secondorder->files){
                              if($secondorder->formType == 'internal'){
                                  echo $this->fetch(
                                          'file',
                                          'printFiles',
                                          array(
                                                  'files' => $secondorder->files,
                                              'fieldset' => 'false',
                                              'object' => null,
                                              'canOperate' => true,
                                              'isAjaxDel' => true
                                          )
                                  );
                              }else{
                                  echo $this->fetch(
                                      'file',
                                      'printFiles',
                                      array(
                                          'files' => $secondorder->files,
                                          'fieldset' => 'false',
                                          'object' => null,
                                          'canOperate' => false,
                                          'isAjaxDel' => true
                                      )
                                  );
                              }

                          }else{
                              echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                          }
                          ?>
                      </div>
                  </div>
              </td>
          </tr>
        <?php if($secondorder->formType == 'internal'): ?>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span></td>
          </tr>
        <?php endif;?>
          <tr>
              <th><?php echo $lang->secondorder->desc;?></th>
              <td colspan='2'><?php echo html::textarea('desc', $secondorder->desc, "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->comment;?></th>
              <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
          </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('sourceBackground', $secondorder->sourceBackground); ?>
<script>
    $(document).ready(function(){
        var isBool = <?php echo $secondorder->formType == 'external' ? 'true' : 'false'; ?>;
        if(isBool){
            window.editor['desc'].readonly(true);
        }
        if(sourceBackground == 'project'){
            $('#cbpProject').parent().removeClass('hidden');
        }else {
            $('#cbpProject').parent().addClass('hidden');
        }
    })
    function sourceBackgroundChange(sourceBackground){
        if(sourceBackground == 'project'){
            $('#cbpProject').parent().removeClass('hidden');
        }else {
            $('#cbpProject').parent().addClass('hidden');

            $.get(createLink('secondorder', 'ajaxGetCbpProjectList'), function(data)
            {
                $('#cbpProject_chosen').remove();
                $('#cbpProject').replaceWith(data);
                $('#cbpProject').chosen();
            });
        }
    }
    $('#type').change(function()
    {
        var type = $(this).val();
        $.get(createLink('secondorder', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            console.log(data);
            $('#subtype_chosen').remove();
            $('#subtype').replaceWith(data);
            $('#subtype').chosen();
        });
    });
    $('#app').change(function(){
        var app = $(this).val();
        $.get(createLink('secondorder', 'ajaxGetUnion', 'app=' + app), function(data)
        {
            $('#union_chosen').remove();
            $('#teamLabel').remove();
            $('#union').replaceWith(data);
            $('#union').chosen();
        });
        $.get(createLink('secondorder', 'ajaxGetTeam', 'app=' + app), function(data)
        {
            $('#team_chosen').remove();
            $('#unionLabel').remove();
            $('#team').replaceWith(data);
            $('#team').chosen();
        });
    })
</script>
<?php include '../../common/view/footer.html.php';?>
