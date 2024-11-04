<?php include '../../common/view/header.html.php';?>
<style>
    .main-table tbody>tr:nth-child(odd){
        background-color: #fff;
    }
    .main-table tbody>tr:nth-child(even){
        background-color: #fbfafc;
    }
</style>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <?php echo   $lang->authoritysystemviewpoint->info . '</span>';?>
            </h2>
        </div>
        <div id="" class="main-row">
                <div class="detail">
                    <div class="detail-content">
                        <table class ='table  table-form'>
                            <tbody>
                            <tr>
                                <th><?php echo  $lang->authoritysystemviewpoint->system;?></th>
                                <td style="white-space:normal"><?php echo html::input('',zget($subSystem,$type),'class="form-control w-p60" readonly','');?></td>
                            </tr>
                            <tr>
                                <th><?php echo  $lang->authoritysystemviewpoint->projectRepository;?></th>
                                <td style="white-space:normal"><?php echo html::input('',$project,'class="form-control w-p60" readonly','');?></td>
                            </tr>
                            <tr>
                                <th><?php echo  $lang->authoritysystemviewpoint->roleName;?></th>
                                <td style="white-space:normal"><?php echo html::input('',zget($lang->myauthority->svnAuthority,$permsissions),'class="form-control w-p60" readonly','');?></td>
                            </tr>
                            <tr>
                                <th><?php echo  $lang->authoritysystemviewpoint->role;?></th>
                                <td style="white-space:normal"><?php echo html::input('',$role,'class="form-control w-p60" readonly','');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->authoritysystemviewpoint->userList;?></th>
                                <td> <?php echo html::input('searchName', $search, "class='form-control w-p60' placeholder={$this->lang->authoritysystemviewpoint->serachTip}  onchange= search(this)" );?></td>
<!--                                <td><input type="search" name="searchName" id = "searchName" class='form-control w-p60' placeholder="--><?php //echo $this->lang->authoritysystemviewpoint->serachTip;?><!--"  onsearch= 'search(this)'></td>-->
                            </tr>
                            </tbody>
                        </table>
                        <form class=" main-table table-nest-collapsed load-indicator main-form form-ajax" method='post'>
                            <table class='table ' id='programList' style ="background-color: #f5f5f5;border-radius: 25px ">
                                <thead>
                                <tr >
                                    <th ><?php echo $lang->authoritysystemviewpoint->account;?></th>
                                    <th ><?php echo $lang->authoritysystemviewpoint->realname ;?></th>
                                    <th ><?php echo $lang->authoritysystemviewpoint->deptName ;?></th>
                                </tr>
                                </thead>
                                <?php foreach ($list as $key => $data):?>
                                    <tr >
                                        <td>
                                            <?php echo $data->account;?>
                                        </td>
                                        <td>
                                            <?php echo $data->realname;?>
                                        </td>
                                        <td><?php echo $data->deptName;?></td>
                                    </tr>
                                <?php endforeach;?>
                            </table>

                        </form>
                        <div class="table-footer">
                            <?php $pager->show('right', 'pagerjs');?>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php
js::set('type',$type);
js::set('project',base64_encode($project));
js::set('role',base64_encode($role));
js::set('thirdid',$thirdid);
?>
<script>
    function search(obj){
        var value = $(obj).val();
        link = createLink('authoritysystemviewpoint', 'authorityusers', 'type=' +type+'&thirdid=' +thirdid+"&search="+value);
        location.href = link;
    }
</script>
<?php include '../../common/view/footer.html.php';?>
