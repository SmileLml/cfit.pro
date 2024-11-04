<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot . 'vue3.js');?>

<style>
    .input-group-addon {
        min-width: 150px;
    }

    .input-group {
        margin-bottom: 6px;
    }

    .panel > .panel-heading {
        color: #333;
        background-color: #f5f5f5;
        border-color: #ddd;
    }

    .panel {
        border-color: #ddd;
    }
    .flex-container{
        display:flex;
        align-items: center;
    }
</style>

<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->environmentorder->create; ?></h2>
        </div>

        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form" id="">
                <tbody>
                <tr >
                    <th><?php echo $lang->environmentorder->title; ?></th>
                    <td colspan='3'><?php echo html::input('title', '', "class='form-control'"); ?></td>

                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->priority; ?></th>
                    <td colspan='3'><?php echo html::select('priority', array('' => '') +$this->lang->environmentorder->priorityList,'', 'class="form-control chosen"'); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->origin; ?></th>

                    <td colspan='3'><?php echo html::select('origin', array('' => '') +$this->lang->environmentorder->originList,'', 'class="form-control chosen"'); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->content; ?></th>
                    <td colspan='3'><?php echo html::input('content', '', "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->finallytime; ?></th>
                    <td colspan='3'><?php echo html::input('finallytime', '', "class='form-control form-datetime'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->description; ?></th>
                    <td colspan='3'>
                        <?php echo html::textarea('description', '', "rows='10' class='form-control' ");?>
                    </td>

                </tr>
                <tr>
                    <th><?php echo $lang->environmentorder->list;?></th>
                    <td colspan='3'>
                        <a style="color:blue" href="javascript:void(0)" @click="addRow(this)" class="btn btn-link"><i class="icon-plus" ></i></a>
                        <table class="table table-form table-bordered">
                            <thead>
                            <tr>
                                <th class="w-60px"><?php echo $lang->environmentorder->rowNum;?></th>
                                <th><?php echo $lang->environmentorder->remark;?></th>
                                <th><?php echo $lang->environmentorder->material;?></th>
                                <th><?php echo $lang->environmentorder->ip;?></th>
                                <th class="w-80px"><?php echo $lang->actions;?></th>
                            </tr>
                            </thead>
                            <tbody >
                            <tr v-for="(row,index) in rows" :key="row.id" >
                                <td>{{index+1}}</td>

                                <td>
                                    <input :name="'remark['+row.id+']'" class='form-control'  v-model="row.remark"/>
                                </td>

                                <td >
                                    <input type="file" accept=".doc,.docx,.png,.jpg,.xls,.xlsx" :name="'files['+row.id+']'" class="form-control" >
                                </td>

                                <td>
                                    <input :name="'ip['+row.id+']'" class='form-control'   v-model="row.ip"/>
                                </td>

                                <td>
                                    <div class="input-group">
                                            <a href="javascript:void(0)" @click="delRow(index)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </div>
                                </td>
                            </tr>

                            <tr id="tipInfo" style="border-top:none; ">
                                <td colspan="5">
                                    <div class="text-left" style="color: red"><span> <?php echo $lang->environmentorder->tipMessage; ?></span></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>

                <tr>
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='3'>
                        <input type="hidden" name="issubmit" value="save">
                        <input type="hidden" name="isWarn" id="isWarn" value="no">
                        <?php
                        echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') .
                            html::commonButton($lang->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::linkButton("返回",$this->session->common_back_url,'self','','btn btn-wide');
                        ?>
                    </td>
                    </td>
                </tr>

                </tbody>
            </table>

        </form>

    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>

