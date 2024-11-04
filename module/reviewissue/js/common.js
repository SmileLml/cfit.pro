/**
 * 设置上传文件是否显示
 *
 *
 * @param reviewType
 */
function  setUploadFileIsShow(reviewType) {
    if(reviewType == 'dept'){ //部门级评审
        $('.uploadFileInfo').show();
    }else {
        $('.uploadFileInfo').hide();
    }
}
