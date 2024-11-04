const {createApp} = Vue;
createApp({
    data() {
        return {
            rows:[],
        };
    },
    methods:{
        addRow(index){
            console.log(this.rows)
            this.rows.push({
                "id":this.rows.length>0?Math.max(...this.rows.map(item=>item.id))+1:0,
                "ip":"",
                "remark":"",
                "material":"",
                })
            console.log(this.rows)
        },
        delRow(index){
            this.rows.splice(index,1)
            console.log(this.rows)
        },
        isValidIpV4(ip){
            if(!ip) return false
            const pattern = /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            const res= pattern.test(ip);
            if(!res){
                alert("请填写正确的 IP 地址")
                return false
            }

            return  true
        },

    },
    mounted(){
        if(rowsList!=''){
            const r=JSON.parse(rowsList)
            if(r.length>0){
                r.map((item,index)=>{
                    item.id=index
                })
                this.rows.push(...r)
                console.log(this.rows)
            }

        }
    },

}).mount("#mainContent");
/**
 * 确认保存
 *
 * @param message
 * @returns {boolean}
 */
function confirmSave(message) {
    $("#isWarn").val("yes");
    if(confirm(message)){
        $("#isWarn").val("no");
        $('#submit').submit();
        return  true;
    }else {
        return false;
    }
}

/**
 * 提交操作
 *
 * @param btnClass
 */
function submitData(btnClass) {
    $('.buttonInfo').attr('type', 'button');
    $('.'+btnClass).attr('type', 'submit');
}





$(".saveBtn").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn');
});

//提交需要校验数据
$(".submitBtn").click(function () {
    $("[name='issubmit']").val("submit");
    var msg = "确认要提交吗，提交后将进入审批环节";
    if(confirm(msg) == true){
        submitData('submitBtn');
    }else{
        return false;
    }
});
