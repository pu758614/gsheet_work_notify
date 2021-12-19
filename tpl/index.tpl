<!DOCTYPE html>
<html>

<head>
    <title>服事提醒小天使-後台設定</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- jQuery UI -->
    <link href="https://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css" rel="stylesheet" media="screen">

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- styles -->
    <link href="css/styles.css" rel="stylesheet">

</head>

<body>
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <!-- Logo -->
                    <div class="logo">
                        <h1><a href="">服事提醒小天使-後台設定</a></h1>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="navbar navbar-inverse" role="banner">
                        <!-- START BLOCK : signing_in -->
                        <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                            <ul class="nav navbar-nav">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">登入中 <b
                                            class="caret"></b></a>
                                    <ul class="dropdown-menu animated fadeInUp">
                                        <!-- <li><a href="profile.html">Profile</a></li> -->

                                        <li><a href='#' onclick="sign_out()">登出</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                        <!-- END BLOCK : signing_in -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            <!-- INCLUDE BLOCK :  menu_list -->
            <div class="col-md-10">

                <div class="row">
                    <div class="col-md-12">
                            <!-- INCLUDE BLOCK : content_page -->
                    </div>
                </div>

            </div>
        </div>
    </div>
<!--
    <footer>
        <div class="container">

            <div class="copy text-center">

            </div>

        </div>
    </footer> -->



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->



    <script>
        function sign_out() {
            $.ajax({
                url: 'gsheet_work_notify_action.php?action=sign_out',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: $("#").serialize(),
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
</body>


</html>