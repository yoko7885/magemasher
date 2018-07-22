<?php
class compose extends base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    public function start()
    {
        $account_id = $_SESSION['MGM_ACCOUNT_ID'];
        $user = commons::get_user($this->db, $account_id);
        
        $this->refresh_sch_pt($user);
        
        $smarty = $this->get_smarty();
        $smarty->assign('user', $user);
        
        $fields = commons::get_fields($this->db);
        $smarty->assign('fields', $fields);
    }

    private function refresh_sch_pt(&$user)
    {
        $sch_pt = commons::get_sch_pt($this->user_db);
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