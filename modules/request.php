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
        $smarty = $this->get_smarty();
        $smarty->assign('callback', $_REQUEST['callback']);
        
        $sch_pt = commons::get_sch_pt($this->user_db);
        $now = $sch_pt['now'];

        $field_id = $_REQUEST['field_id'];
        $field = commons::get_fields($this->db, $field_id);

        $json = new myjson();
        if ($now < $field['need_sch_pt'])
        {
            $message = 'search pt が不足しています。';
            $json->set('code','2')->set('message', $message);
        }
        else
        {
            $item = $this->get_searched_item();
            $this->update_user($field, $item, $now);
            
            $json->set('code','1')->set('item', $item)->set('now', $now);
        }
        $smarty->assign('json', $json->get());
    }
    
    private function update_user($field, $item, &$now)
    {
        $need_sch_pt = $field['need_sch_pt'];
        $now = $now - $need_sch_pt;

        $values = array
        (
            'now' => (String)$now
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
        
        $result = $this->user_db->insert
        (
            array
            (
                'table' => 'bag'
                , 'values' => $item
            )
        );
        if (!$result)
        {
            die('An error occurred, txtSQL said: '.$this->user_db->get_last_error());
        }
    }

    private function get_searched_item()
    {
        $sch_data = $this->db->select
        (
            array
            (
                'table' => 'sch_itm_base_field1'
            )
        );
        
        $sum_coefficient = 0;
        
        foreach($sch_data as &$item)
        {
            $item['coefficient']
                = ((100-$item['scarcity'])*10)
                + ((100-$item['size']))
                - ($item['holy']) - ($item['wicked']);
                
            $sum_coefficient += $item['coefficient'];
        }
        
        $last_rate = 0;
        mt_srand();
        $rand_result = mt_rand(0, 100000);
        
        foreach($sch_data as &$item)
        {
            $now_rate = $item['coefficient'] / $sum_coefficient * 100000;
            $next_rate = $last_rate + $now_rate;
            unset($item['coefficient']);
            
            if ($last_rate < $rand_result && $rand_result <= $next_rate)
            {
                return $item;
            }
            $last_rate = $next_rate;
        }
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