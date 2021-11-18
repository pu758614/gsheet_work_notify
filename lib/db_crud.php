<?php
namespace DB_CRUD;
Trait DB_CRUD {
    /**
     * 取得一筆資料(一條件)
     * @param  string  $table 資料表
     * @param  string  $key   欄位
     * @param  string  $value 值
     * @param  string  $sort  排列條件
     * @param  string  $order 排列順序
     * @return array
     */
    function getSingleById($table,$key,$value,$sort='',$order=''){
        $order_by = '';
        if($sort != ''){
            $order_by = 'ORDER BY '.$sort.' '.$order;
        }

        $arr_prestr = array($value);
        $sql = "SELECT * FROM $table WHERE $key=? $order_by";
        $result = $this->db->Execute($sql,$arr_prestr);
        if($result && $result->RecordCount() > 0){
            return $result->FetchRow();
        }else{
            return array();
        }
    }
    /**
     * 取得多筆資料(一條件)
     * @param  string  $table   資料表
     * @param  string  $key     欄位
     * @param  string  $value   值
     * @param  string  $sort    排列條件
     * @param  string  $order   排列順序
     * @return array
     */
    function getArrayById($table,$key,$value,$sort='',$order=''){
        $order_by = '';
        if($sort != ''){
            $order_by = 'ORDER BY '.$sort.' '.$order;
        }

        $arr_prestr = array($value);
        $sql = "SELECT * FROM $table WHERE $key=? $order_by";
        $result = $this->db->Execute($sql,$arr_prestr);
        if($result && $result->RecordCount() > 0){
            return $result->getAll();
        }else{
            return array();
        }
    }
    /**
     * 取得一筆資料(多條件)
     * @param  string  $table       資料表
     * @param  array   $condition   條件
     * @param  string  $sort        排列條件
     * @param  string  $order       排列順序
     * @return array
     */
    function getSingleByArray($table,$condition,$sort='',$order=''){
        $where = '';
        $arr_prestr = array();
        $tmp_in = isset($condition['in']) && is_array($condition['in'])? $condition['in']: array();
        $tmp_str = array();
        unset($condition['in']);
        foreach ($condition as $key => $value) {
            $tmp_str[] = $key . ' = ? ';
            $arr_prestr[] = $value;
        }
        if(!empty($tmp_in)){
            foreach ($tmp_in as $tmp_key => $tmp_cond_arr) {
                $tmp_in_cond = array();
                foreach ($tmp_cond_arr as $tmp_cond) {
                    $tmp_in_cond[] = '?';
                    $arr_prestr[] = $tmp_cond;
                }
                $tmp_str[] = $tmp_key.' IN ('.implode(' ,', $tmp_in_cond).')';
            }
        }
        $where = implode(' AND ', $tmp_str);
        $order_by = '';
        if($sort != ''){
            $order_by = 'ORDER BY '.$sort.' '.$order;
        }

        $sql = "SELECT * FROM $table WHERE $where $order_by";
        $result = $this->db->Execute($sql,$arr_prestr);
        if($result && $result->RecordCount() > 0){
            return $result->FetchRow();
        }else{
            return array();
        }
    }


    /**
     * 取得一筆資料(多條件)
     * @param  string  $table       資料表
     * @param  array   $condition   條件
     * @param  string  $sort        排列條件
     * @param  string  $order       排列順序
     * @return array
     */
    function getArrayByArray($table,$condition=array(),$sort='',$order=''){
        $where = '';
        $arr_prestr = array();
        $tmp_in = isset($condition['in']) && is_array($condition['in'])? $condition['in']: array();
        $tmp_str = array();
        unset($condition['in']);
        foreach ($condition as $key => $value) {
            $tmp_str[] = $key . ' = ? ';
            $arr_prestr[] = $value;
        }
        if(!empty($tmp_in)){
            foreach ($tmp_in as $tmp_key => $tmp_cond_arr) {
                $tmp_in_cond = array();
                foreach ($tmp_cond_arr as $tmp_cond) {
                    $tmp_in_cond[] = '?';
                    $arr_prestr[] = $tmp_cond;
                }
                $tmp_str[] = $tmp_key.' IN ('.implode(' ,', $tmp_in_cond).')';
            }
        }
        $where = implode(' AND ', $tmp_str);

        $order_by = '';
        if($sort != ''){
            $sort_field = $db->tableField($sort);
            $order_by = 'ORDER BY '.$sort_field.' '.$order;
        }
        if($condition!= array() || $where!=''){
            $where = "WHERE $where";
        }

        $sql = "SELECT * FROM $table  $where $order_by";


        $result = $this->db->Execute($sql);

        if($result && $result->RecordCount() > 0){
            return $result->getAll();
        }else{
            return array();
        }
    }
    /**
     * 新增一筆資料(insert)
     * @param  string  $table 資料表
     * @param  array   $data  新增資料
     * @return array
     */
    function insertData($table,$data){
        $column_arr = $arr = '';
        $arr_prestr = array();

        foreach ($data as $key => $value) {
            $arr.= '?,';
            $column_arr.= $key.',';
            array_push($arr_prestr,$value);
        }
        $arr        = substr($arr,0,-1);
        $column_arr = substr($column_arr,0,-1);

        $sql = "INSERT INTO $table ($column_arr) VALUES ($arr)";
        $result = $this->db->Execute($sql,$arr_prestr);
        if($result){
            $result_id = $this->db->_insertid();
            if( $result_id!=0 ){
                return $result_id;
            }else{
                return 1;
            }
        }return 0;
    }
    /**
     * 新增一筆資料(replace)
     * @param  string  $table 資料表
     * @param  array   $data  新增資料
     * @return array
     */
    function replaceData($table,$data){
        $column_arr = $arr = '';
        $arr_prestr = array();

        foreach ($data as $key => $value) {
            $arr.= '?,';
            $column_arr.= $key.',';
            array_push($arr_prestr,$value);
        }
        $arr        = substr($arr,0,-1);
        $column_arr = substr($column_arr,0,-1);

        $sql = "REPLACE INTO $table ($column_arr) VALUES ($arr)";
        $result = $this->db->Execute($sql,$arr_prestr);
        if($result){
            $result = $this->db->_insertid();
            if( $result==0 ){
                $result = 1;
            }
            return $result;
        }return 0;
    }
    /**
     * 更新一筆資料
     * @param  string  $table     資料表
     * @param  array   $data      更新資料
     * @param  array   $condition 條件
     * @return boolean
     */
    function updateData($table,$data,$condition) {
        $where = $column = '';
        $arr_prestr = array();

        foreach ($data as $key => $value) {
            $column.= $key.'=?,';
            array_push($arr_prestr,$value);
        }
        $column = substr($column,0,-1);

        if(!is_array($condition)){
            return false;
        }

        foreach ($condition as $field_key => $field_val) {
            if($field_key != 'in'){
                $where.= $field_key.'=?';
                array_push($arr_prestr,$field_val);
            }else{
                //in條件式
                foreach ($field_val as $in_key => $in_val) {
                    $where.= $in_key.' IN ( ';
                    foreach ($in_val as $value) {
                        $where.= '?,';
                        array_push($arr_prestr,$value);
                    }
                }
                $where = substr($where,0,-1);
                $where.=' )';
            }
            $where.= ' AND ';
        }
        $where = substr($where,0,-4);

        $sql = "UPDATE $table SET $column WHERE $where";
        $result = $this->db->Execute($sql,$arr_prestr);

        if($result){
            return true;
        }return false;
    }
    /**
     * 刪除一筆資料
     * @param  string  $table     資料表
     * @param  array   $condition 條件
     * @return boolean
     */
    function deleteData($table,$condition){
        $where = '';
        $arr_prestr = array();
        foreach ($condition as $key => $value) {
            $where.= $key.'=? AND ';
            array_push($arr_prestr,$value);
        }
        $where = substr($where,0,-4);

        $sql = "DELETE FROM $table WHERE $where";
        $result = $this->db->Execute($sql,$arr_prestr);

        if($result){
            return true;
        }return false;
    }
}