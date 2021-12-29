<?php



$data_list = $db->getNotifyRecordList();

$action_set = array(
    'set'    => '設定',
    'notify' => '通知',
);

$line_user_tmp = array();


foreach ($data_list as $key => $data) {
    $user_info = $db->getSingleById('sheet_notify_user','line_user_uuid',$data['line_user_uuid']);
    $line_name = isset($user_info['line_name'])?$user_info['line_name']:'';
    $tpl->newBlock('date_list');
    $tpl->assignArray(array(
        'no'          => $key+1,
        'real_name'   => htmlspecialchars($data['real_name']),
        'create_time' => htmlspecialchars($data['create_time']),
        'line_name'   => htmlspecialchars($line_name),
        'msg'         => htmlspecialchars($data['msg']),
        'action'      => isset($action_set[$data['type']])?$action_set[$data['type']]:'',
    ));
}
