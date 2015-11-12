<?php
class request extends base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    public function start(){}
    
    public function search()
    {
        $result = '1';
        $smarty = $this->get_smarty();
        $smarty->assign('callback', $_REQUEST['callback']);
        $smarty->assign('json', json_encode($result));
    }

    private function get_user($account_id)
    {
        $result = $this->db->select
        (
            array
            (
                'table' => 'user', 
                'where' => array
                (
                    "account_id = \"{$account_id}\""
                    , "and"
                    , "invalid_flg != 1"
                )
            )
        );
        return $result;
    }

    private function refresh_sch_pt(&$user)
    {
        $sch_pt = $this->user_db->select
        (
            array
            (
                'table' => 'sch_pt'
            )
        );

        if (count($sch_pt) <= 0) return;
        $sch_pt = $sch_pt[0];
        $time_now = time();

        $diff = $time_now - $sch_pt['last'];

        $one = 60 * 4; // 4分
        $up = $diff / $one;
        
        $sum = round($up + $sch_pt['now'], 2);
        
        if ($user['sch_pt'] < $sum) $sum = $user['sch_pt'];
        
        $values = array
        (
            'now' => (String)$sum
            , 'last' => (String)$time_now
        );

        $result = $this->user_db->update
        (
            array
            (
                'table' => 'sch_pt'
                , 'where' => array('last >= 0')
                , 'values' => $values
            )
        );

        if (!$result)
        {
            die('An error occurred, txtSQL said: '.$this->user_db->get_last_error());
        }
        
        $user['now_pt'] = $sum;
    }
}
?>