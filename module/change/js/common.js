/**
 * 设置级别
 */
function changeLevel(){
    setCategorySelect();
    setSubCategoryShow();
    setReviewNodes();
    setProjectPlanshow();
}

/**
 * 变更项目分类
 */
function changeCategory() {
    setSubCategoryShow(); //二级子分类是否展示
    setReviewNodes();
}

/**
 * 变更是否是主项目
 */
function changeIsMasterPro() {
    setMailUsersRequire();
}

/**
 * 设置子分类是否显示
 */
function setSubCategoryShow() {
    var category  = $('#category').val();      //分类
    if(category == 'plan'){
        $('#subCategoryDiv').addClass('required');
        $('#subCategoryDiv').removeClass('hidden');
    }else {
        $('#subCategoryDiv').removeClass('required');
        $('#subCategoryDiv').addClass('hidden');
        $('#subCategory').val('');
        $('#subCategory').chosen();
    }
}

/**
 * 设置邮件人是否必选
 */
function setMailUsersRequire() {
    var isMasterPro  = $('#isMasterPro').val();      //分类
    if(isMasterPro == 1){
        $('#mailUsersTd').addClass('required');
    }else {
        $('#mailUsersTd').removeClass('required');
    }
}



/**
 * 设置变更分类是否显示“其他选项”
 */
function setCategorySelect() {
    var level    = $('#level').val(); //级别
    var category = $('#category').val();        //分类
    $.get(createLink('change', 'ajaxGetCategoryList', "level=" + level + "&category=" + category) , function(data) {
        $('#category_chosen').remove();
        $('#category').replaceWith(data);
        $('#category').chosen();
    });
}
//设置节点是否显示
function setReviewNodes() {
    var level             = $('#level').val(); //级别
    var category          = $('#category').val();        //分类
    var isInteriorPro     = $('#isInteriorPro').val(); //是否内部项目（自建）
    var isSlavePro        = $('#isSlavePro').val();   //是否是从项目
    if(!level){
        $('.nodes').addClass('hidden');
        $('.node-item').addClass('hidden');
        return true;
    }

    $('.nodes').removeClass('hidden');
    $('.node-item').removeClass('hidden');
    $('.nodeqa').addClass('hidden'); //qa不在审核节点 20231124

    //是否主项目
    if(isSlavePro != 1){
        $('.nodemasterProPm').addClass('hidden');
    }else {
        $('.nodemasterProPm').removeClass('hidden');
    }

    if(level == 1) {
        $('.nodepm').addClass('hidden');
        $('.nodepdManage').addClass('hidden');
        $('.nodeframeworkManage').addClass('hidden');
        if((isInteriorPro == 2) && (category == 'plan')){
            $('.nodepdManage').removeClass('hidden');
            $('.nodeframeworkManage').removeClass('hidden');
        }
    } else if(level == 2) {
        $('.nodepm').addClass('hidden');
        $('.nodepdManage').addClass('hidden');
        $('.nodeframeworkManage').addClass('hidden');
        $('.nodeowner').addClass('hidden');
    }
    else if(level == 3) {
        $('.nodedeptManage').addClass('hidden');
        $('.nodepdManage').addClass('hidden');
        $('.nodeframeworkManage').addClass('hidden');
        $('.nodedeptLeader').addClass('hidden');
        $('.nodeowner').addClass('hidden');
    }
    //调用是否设置必须传字段的值
    setReviewRequiredNodes();
    return true;
}

/**
 * 设置必传节点
 */
function setReviewRequiredNodes(){
    $('.node-item').each(function () {
        var nodeKeyName = $(this).attr('id');
        if($(this).hasClass('hidden')){
            $('#requiredNodes-'+nodeKeyName).val('0');
        }else {
            $('#requiredNodes-'+nodeKeyName).val('1');
        }
    });
    return true;
}

/**
 * 设置忽略审核节点
 * @param nodeKey
 */
function setSkipReviewNode(nodeKey){
    if($('#skipReviewNode_'+nodeKey).prop('checked')){
        $('#nodes'+nodeKey).attr("disabled","disabled");
        $('#nodes'+nodeKey).val('').trigger('chosen:updated');
    }else {
        var currentNodeReviewers = eval('node_' + nodeKey + '_reviewers');
        var reviewersArray = new Array();
        if(currentNodeReviewers != ''){
            reviewersArray = currentNodeReviewers.split(',');
        }
        $('#nodes'+nodeKey).removeAttr("disabled");
        $('#nodes'+nodeKey).val(reviewersArray).trigger('chosen:updated');
    }
}

function setProjectPlanshow(){
    let level    = $('#level').val(); //级别
    if(level == 1){
        $("#projectplanshowpanel").removeClass('hidden')
    }else{
        $("#projectplanshowpanel").addClass('hidden')
    }
}