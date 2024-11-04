<?php
/**
 * The create view of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->build->create;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' id='dataform' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
       <tr>
             <th class="w-150px"><?php echo $lang->build->warm;?></th>
             <td style="color: red" colspan='4'> <?php echo $this->lang->build->warmTip ?></td>
       </tr>
        <tr>
              <th><?php echo $lang->build->app;?></th>
              <td colspan='4'><?php echo html::select('app', $apps, '',"class='form-control chosen' ");?></td>
        </tr>
          <tr>
              <th><?php echo $lang->build->product;?></th>
              <td colspan='2'><?php echo html::select('product', $products, empty($product) ? '' : $product->id, 'class="form-control chosen" required');?></td>
              <td colspan='2'>
                <div class='input-group'>
                  <span class='input-group-addon'><?php echo $lang->build->version; ?></span>
                  <?php echo html::select('version', $plans, '', 'class="form-control chosen" required onchange = "getversion()"');?>
                </div>
              </td>
          </tr>
          <tr>
              <th ><?php echo $lang->build->range;?></th>
              <td colspan='4'>
                  <div class='input-group top'>
                      <span class='input-group-addon'><?php echo $lang->build->demandid; ?></span>
                      <?php echo html::select('demandid[]', '','' ,"class='form-control chosen' multiple");?>
                      <input type="hidden" class="form-control"  value="" id="demandChosen" name = "demandChosen">
                  </div>
                  <div class='input-group top'>
                      <span class='input-group-addon'><?php echo $lang->build->problemid; ?></span>
                      <?php echo html::select('problemid[]', '', '',"class='form-control chosen' multiple ");?>
                      <input type="hidden" class="form-control"  value="" id="problemChosen" name = "problemChosen">
                  </div>
                  <div class='input-group top'>
                      <span class='input-group-addon'><?php echo $lang->build->sendlineId; ?></span>
                      <?php echo html::select('sendlineId[]', '','' ,"class='form-control chosen' multiple");?>
                      <input type="hidden" class="form-control"  value="" id="sendlineChosen" name = "sendlineChosen">
                  </div>
                  <span style="color: red;" colspan='4'><?php echo $this->lang->build->buildTip ?></span>
              </td>
          </tr>
        <!--<tr>
          <th><?php /*echo $lang->build->product;*/?></th>
          <?php /*if(!empty($products)):*/?>
          <td>
            <div class='input-group' id='productBox'>
              <?php /*echo html::select('product', $products, empty($product) ? '' : $product->id, " class='form-control chosen' required");*/?>
            </div>
          </td>
              <th><?php /*echo $lang->build->version;*/?></th>
              <td>
                  <div class=' ' >
                      <div class='input-group'>
                          <?php /*echo html::select('version', $plans,'', "class='form-control chosen' required");*/?>
                          <span class="input-group-btn fix-border "><a href="javascript:;" class="btn addItem" id="proandver" style="width:30px"><i class="icon-refresh"></i></a></span>
                          <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:40px"><i class="icon-help" title="<?php /*echo $lang->demand->createPlanTips*/?>"></i></a></span>
                           <span class="input-group-btn"><?php /*echo html::a($this->createLink('productplan','create',"productID =$product->id"),'<i class="icon-plus" title=""></i>'. $lang->demand->newversion,'_blank','class="btn btn-info" data-app="product" onclick="return createplan()')*/?></span>
                      </div>
                  </div>
              </td>
          <?php /*endif;*/?>
          <td></td>
        </tr>-->
        <!--<tr>
              <th><?php /*echo $lang->build->purpose;*/?></th>
              <td colspan='2'><?php /*echo html::select('purpose', $lang->build->purposeList, '', 'class="form-control chosen"');*/?></td>
            <td colspan='2'>
                <div class='input-group'>
                    <span class='input-group-addon'><?php /*echo $lang->build->rounds;*/?></span>
                    <?php /*echo html::select('rounds', $lang->build->roundsList, '', 'class="form-control chosen"');*/?>
                </div>
            </td>
        </tr>-->
        <tr>
              <th><?php echo $lang->build->builder;?></th>
              <td colspan='2'><?php echo html::select('builder', $users, $cm,'class="form-control chosen" required');?></td>
            <td colspan='2'>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->build->testUser;?></span>
                    <?php echo html::select('testUser', $users, '', 'class="form-control chosen" required');?>
                </div>
            </td>
        </tr>
          <?php if($isSetSeverityTestUser):?>
              <tr>
                  <th><?php echo $lang->build->severityTestUser;?></th>
                  <td colspan='3'>
                      <div class='input-group top'>
                          <?php echo html::select('severityTestUser', array('' => '') + $severityTestUsers, '','class="form-control chosen" required');?>
                      </div>

                      <?php if (empty($severityTestUsers)):?>
                          <span style="color: red;" colspan='3'><?php echo $this->lang->build->severityTestUsersTip ?></span>
                      <?php endif;?>
                  </td>
                  <td colspan='2'></td>

              </tr>
          <?php endif;?>
       <!-- <tr >
              <th><?php /*echo $lang->demand->systemverify;*/?></th>
              <td colspan='2'><?php /*echo html::radio('systemverify', $lang->build->needOptions, '0','onchange ="systemveif()"');*/?></td>
              <td class="test" colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php /*echo $lang->build->verifyUser;*/?></span>
                      <?php /*echo html::select('verifyUser', $users, '', "class='form-control chosen' ");*/?>
                  </div>
              </td>
        </tr>-->
        <tr>
              <th><?php echo $lang->build->scmPath;?></th>
              <td colspan='4'><?php echo html::textarea('scmPath', '', "class='form-control' placeholder='{$lang->build->placeholder->scmPath}' rows='2'");?></td>
        </tr>
        <tr>
              <th><?php echo $lang->build->svnPath;?></th>
              <td colspan='4'><?php echo html::textarea('svnPath', '', "class='form-control' placeholder='{$lang->build->placeholder->svnPath}' rows='2'");?></td>
        </tr>
        <tr>
              <th><?php echo $lang->build->buildManual;?></th>
              <td colspan='4'><?php echo html::input('buildManual', '', "class='form-control' placeholder='{$lang->build->placeholder->buildManual}'");?></td>
        </tr>
       <!-- <tr>
              <th><?php /*echo $lang->build->taskName;*/?></th>
              <td colspan='4'><?php /*echo html::select('taskName', '','', "class='form-control' required onchange=taskNameChange()");*/?>
              <span style="color: lightslategray;" colspan='4'><?php /*echo $this->lang->build->taskTip*/?></span>
              </td>
              <input type="hidden" value="" id="taskid" name="taskid">
              <input type="hidden" value="" id="taskname" name="taskname">
        </tr>-->
        <tr>
          <th><?php echo $lang->comment;?></th>
          <td colspan='4'><?php echo html::textarea('desc', '', "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
        </tr>
        <tr>
          <input type="hidden" name="issubmit" value="save">
          <td colspan="5" class="text-center form-actions">
            <?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn')?>
            <?php echo html::commonButton($lang->build->submit, '', 'btn btn-wide btn-primary submitBtn');?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script>
    var clickTimer = 0;
    //保存不需要校验数据
    $(".saveBtn").click(function () {
        var interval = 3000;
        var now = new Date();
        var timer = clickTimer;
        if(now - timer < interval){
            return false;
        }else{
            clickTimer = now;
            $("[name='issubmit']").val("save");
            $('#dataform').submit();
        }
    });

    //提交需要校验数据
    $(".submitBtn").click(function () {
        var interval = 3000;
        var now = new Date();
        var timer = clickTimer;
        if(now - timer < interval){
            return false;
        }else{
            clickTimer = now;
            $("[name='issubmit']").val("submit");
            $('#dataform').submit();
        }
    });
   //重置选项
    $("form").submit(function(){
        changeproblemid();
        changedemandid();
        changesendlineId();
    })
</script>
<?php
 js::set('productGroups', $productGroups) ;
 js::set('projectID', $projectID);
 js::set('problemID', $problems ? array_keys($problems) :'');
 js::set('demandID', $demands ? array_keys($demands) : '');
 js::set('secondID', $secondorder ? array_column($secondorder,'id') : '');
?>
<?php include '../../../common/view/footer.html.php';?>
