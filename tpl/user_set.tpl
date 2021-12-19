<style>

    table td{
        vertical-align: middle;

        text-align: center;
        font-size:16px;
    }

    table th{
        vertical-align: middle;
        text-align: center;
        font-size:16px;
    }
</style>
<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">人員設定</div>
        <div class="col-md-5"></div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group form">
                        <input type="text" class="form-control" placeholder="關鍵字搜尋...">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">搜尋</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table class="table" style="border:3px #cccccc solid;"  cellpadding="10" border='1'>
            <thead>
                <tr>
                    <th width='5%' style="vertical-align: middle;">#</th>
                    <th width='15%' style="vertical-align:middle;">line名稱</th>
                    <th width='15%'>姓名</th>
                    <th width='40%' style="vertical-align:middle;">服事表名稱</th>
                    <th width='10%' style="vertical-align:middle;">通知日</th>
                    <th width='10%' style="vertical-align:middle;">狀態</th>
                    <th width='20%'>動作</th>
                </tr>
            </thead>
            <tbody>
                <!-- START BLOCK : user_list -->
                <tr class="">
                    <td style="vertical-align: middle;">{no}</td>
                    <td>{line_name}</td>
                    <td class='{uuid}_view'>{name}</td>
                    <td class='{uuid}_view' id='{uuid}_sheet_name'>
                        {sheet_names}
                    </td>
                    <td class='{uuid}_view' id='{uuid}_notify_date'>
                        {notify_day}
                    </td>

                    <td class='{uuid}_view' id='{uuid}_status'>
                        {status}
                    </td>
                    <td class='{uuid}_view'>
                        <button class="btn btn-primary edit_but" onclick="edit_text('{uuid}')"><i
                                class="glyphicon glyphicon-pencil"></i> 編輯</button>
                    </td>
                    <td class='{uuid}_edit' style="display: none;">
                        <div class="col-sm-16">
                            <input type="text" id='{uuid}_name' class="form-control" value="{name}"
                                placeholder="">
                        </div>
                    </td>
                    <td class='{uuid}_edit' style="display: none;">
                        <div class="col-sm-16">
                            <input type="text" id='{uuid}_text' class="form-control" value="{sheet_names}"
                                placeholder="">
                        </div>
                    </td>
                    <td class='{uuid}_edit'  style="display: none;" >
                        <div class="col-sm-16" >
                            <select name="" id="{uuid}_notify_day" id=>
                                <option value="0" {notify_day_0}>週日</option>
                                <option value="1" {notify_day_1}>週一</option>
                                <option value="2" {notify_day_2}>週二</option>
                                <option value="3" {notify_day_3}>週三</option>
                                <option value="4" {notify_day_4}>週四</option>
                                <option value="5" {notify_day_5}>週五</option>
                                <option value="6" {notify_day_6}>週六</option>
                            </select>
                        </div>
                    </td>
                    <td class='{uuid}_edit' style="display: none;">
                        <div class="col-sm-16">
                            <div>
                                <input type="radio" name="{uuid}_enable_notify" id="" value="1" {enable_notify_1}>啟動<p></p>
                            </div>
                            <div>
                                <input type="radio" name="{uuid}_enable_notify" id="" value="0" {enable_notify_0}>關閉
                            </div>

                        </div>
                    </td>


                    <td class='{uuid}_edit' style="display: none;">
                        <div>
                            <button class="btn btn-info" onclick="save_text('{uuid}')"><i
                                class="glyphicon glyphicon-save"></i> 儲存</button>
                        </div><p>
                        <div>
                            <button class="btn btn-danger" onclick="close_text('{uuid}')"><i
                                class="glyphicon glyphicon-remove"></i> 關閉</button>
                        </div>

                    </td>
                </tr>
                <!-- END BLOCK : user_list -->
            </tbody>
        </table>
    </div>
</div>

<script>
    function edit_text(uuid) {
        $("." + uuid + "_view,.edit_but").hide();
        $("." + uuid + "_edit").show();
    }

    function close_text(uuid) {
        $("." + uuid + "_view,.edit_but").show();
        $("." + uuid + "_edit").hide();
    }

    function save_text(uuid) {
        let status = $("input[name="+uuid+"_enable_notify]:checked").val();
        $.ajax({
            url: 'gsheet_work_notify_action.php?action=save_text',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                uuid: uuid,
                text: $("#" + uuid + "_text").val(),
                notify_day:$("#" + uuid + "_notify_day").val(),
                status:status
            },
        })
        .done(function (result) {
            if (result.error == false) {
                $("#" + uuid + "_sheet_name").text(result.data);
                let notify_day_str =  $('#'+uuid+'_notify_day :selected').text();
                $("#"+uuid+"_notify_date").text(notify_day_str);
                if(status==0){
                    $("#"+uuid+"_status").text('關閉')
                }else{
                    $("#"+uuid+"_status").text('啟動')
                }
                close_text(uuid);
            } else {
                alert(result.msg);
            }
        })
        .fail(function () {
            alert('發生錯誤');
        });
    }
</script>