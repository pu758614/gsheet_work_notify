<style>

    table td{
        vertical-align: middle;
        text-align: center;
    }
    table th{
        vertical-align: middle;
        text-align: center;
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
                    <th width='50%' style="vertical-align:middle;">服事表名稱</th>
                    <th width='10%'>動作</th>
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

                    <td class='{uuid}_view'>
                        <button class="btn btn-primary edit_but" onclick="edit_text('{uuid}')"><i
                                class="glyphicon glyphicon-pencil"></i> 編輯</button>
                    </td>
                    <td class='{uuid}_edit' style="display: none;">
                        <button class="btn btn-info" onclick="save_text('{uuid}')"><i
                                class="glyphicon glyphicon-save"></i> 儲存</button>
                        <button class="btn btn-danger" onclick="close_text('{uuid}')"><i
                                class="glyphicon glyphicon-remove"></i> 關閉</button>
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
        $.ajax({
            url: 'gsheet_work_notify_action.php?action=save_text',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                uuid: uuid,
                text: $("#" + uuid + "_text").val()
            },
        })
            .done(function (result) {
                if (result.error == false) {
                    $("#" + uuid + "_sheet_name").text(result.data);
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