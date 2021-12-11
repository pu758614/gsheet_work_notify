<div class="page-content container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-wrapper">
                <div class="box">
                    <div class="content-wrap">
                        <h6>登入系統</h6>

                        <input class="form-control" id='login_name' type="text" placeholder="帳號">
                        <input class="form-control" id='login_password' type="password" placeholder="密碼">
                        <div class="action">
                            <a class="btn btn-primary signup" onclick="login_system()">登入</a>
                        </div>
                    </div>
                </div>

                <!-- <div class="already">
                    <p>Don't have an account yet?</p>
                    <a href="signup.html">Sign Up</a>
                </div> -->
            </div>
        </div>
    </div>
</div>
<script>
    function login_system() {
        $.ajax({
        url: 'gsheet_work_notify_action.php?action=login_system',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {
            login_name:$("#login_name").val(),
            login_password:$("#login_password").val(),
        },
        })
        .done(function(result) {
            if( result.error==false ){
                window.location.reload();
            }else{
                alert(result.msg);
            }
        })
        .fail(function() {
            alert('發生錯誤');
        });
    }

</script>