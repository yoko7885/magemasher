<?php

class commons
{
    public static function get_user($db, $account_id = null)
    {
        $where = array();
        if ($account_id)
        {
            $where[] = "account_id = \"{$account_id}\"";
            $where[] = "and";
        }
        $where[] = "invalid_flg != 1";
        
        $result = $db->select
        (
            array
            (
                'table' => 'user', 
                'where' => $where
            )
        );
        if ($account_id)
        {
            if (count($result) > 0) $result = $result[0];
        }
        return $result;
    }

    public static function get_fields($db, $field_id = null)
    {
        $where = array();
        if ($field_id)
        {
            $where[] = "field_id = \"{$field_id}\"";
            $where[] = "and";
        }
        $where[] = "invalid_flg != 1";
        
        $result = $db->select
        (
            array
            (
                'table' => 'field', 
                'where' => $where
            )
        );
        if ($field_id)
        {
            if (count($result) > 0) $result = $result[0];
        }
        return $result;
    }
    
    public static function get_sch_pt($db)
    {
        $sch_pt = $db->select
        (
            array
            (
                'table' => 'sch_pt'
            )
        );
        
        $time_now = time();

        if (count($sch_pt) <= 0) return array('now' => 0, 'last' => $time_now);
        return $sch_pt[0];
    }
}
?>