/**
 * 设置菜单高亮
 * @param action
 */
function setMenuHighlight(action){
    if(action == 'fix'){
        $("li[data-id='gain']").removeClass('active');
        $("li[data-id='fix']").addClass('active');
    }else {
        $("li[data-id='fix']").removeClass('active');
        $("li[data-id='gain']").addClass('active');
    }
}
