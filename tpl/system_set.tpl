<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">系統設定</div>
    </div>
    <div class="panel-body">
        <form id='data_form' class="form-horizontal" role="form">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" value="{password}" name="password"  id="password" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">確認密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" value="{password}" id="password_check" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">google試算表代號<p>(務必開啟可檢視權限)</p></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{sheet_code}" name="sheet_code" id="sheet_code" placeholder="">
                </div>
                <div class=" col-sm-10">
                    <p><p></p>
                    <input type="button" class="btn btn-info" onclick="test_sheet()" value="測試試算表連結">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="button" onclick="save()" class="btn btn-primary">儲存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    function test_sheet() {

        let sheet_code = $("#sheet_code").val();
        $.ajax({
            url: 'gsheet_work_notify_action.php?action=test_sheet',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                sheet_code:sheet_code
            },
        })
        .done(function(result) {
            if( result.error==false ){
                alert('有效的google試算表代號');
            }else{
                alert(result.msg);
            }
        })
        .fail(function() {
            alert('發生錯誤');
        });
    }

    function save() {
        let password = $("#password").val();
        let password_check = $("#password_check").val();
        if(password==''){
            alert('密碼不能為空');
            return false;
        }
        if(password!=password_check){
            alert('密碼與確認密碼不同');
            return false;
        }
        let sheet_code = $("#sheet_code").val();
        $.ajax({
            url: 'gsheet_work_notify_action.php?action=config_setting',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: $("#data_form").serialize(),
        })
        .done(function(result) {
            if( result.error==false ){
              // window.location.reload();
            }else{
               // alert(result.msg);
            }
        })
        .fail(function() {
            alert('發生錯誤');
        });
    }
</script>