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
        <div class="panel-title">通知紀錄</div>
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
                    <th width='15%' style="vertical-align:middle;">姓名(line名稱)</th>
                    <th width='40%' style="vertical-align:middle;">內容</th>
                    <th width='15%' style="vertical-align:middle;">通知時間</th>
                    <th width='5%'>動作</th>
                </tr>
            </thead>
            <tbody>
                <!-- START BLOCK : date_list -->
                <tr class="">
                    <td style="vertical-align: middle;">{no}</td>
                    <td>{real_name}({line_name})</td>
                    <td class=''>{msg}</td>
                    <td class='' >
                        {create_time}
                    </td>
                    <td class='{uuid}_view' id='{uuid}_notify_date'>
                        {action}
                    </td>
                </tr>
                <!-- END BLOCK : date_list -->
            </tbody>
        </table>
    </div>
</div>

<script>

</script>