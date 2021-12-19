<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">系統設定</div>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">確認密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password_check" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">google試算表代號<p>(務必開啟可檢視權限)</p></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sheet_code" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" onclick="save()" class="btn btn-primary">儲存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
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
    }
</script>