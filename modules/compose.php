<?php
class compose extends base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    public function start($request)
    {
        $smarty = $this->get_smarty();
        $smarty->assign('as', utils::crypt('compose'));
        $smarty->assign('do', utils::crypt('start'));
        
        $account_id = $_SESSION['MGM_ACCOUNT_ID'];
        $user = commons::get_user($this->db, $account_id);
        
        $this->refresh_sch_pt($user);
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
    
    public function execute($request)
    {
        $smarty = $this->get_smarty();
        $smarty->assign('as', utils::crypt('compose'));
        $smarty->assign('do', utils::crypt('complete'));
        
        $lists = json_decode($request["targets"], true);
        
        $sum_value = $this->get_item_array();
        $item_count = count($lists);
        
        foreach($lists as $i => $value)
        {
            $lists[$i] = commons::get_bag_one($this->user_db, $value);
            $sum_value = $this->sum_item_array($sum_value, $lists[$i]);
        }
        // 平均値
        $average_value = $this->average_item_array($sum_value, $item_count);
        // 通常アイテム
        $normal_value = $this->normal_item_array($average_value);
        // 神聖アイテム
        $holy_value = $this->holy_item_array($average_value);
        // 邪悪アイテム
        $dark_value = $this->dark_item_array($average_value);
        // 超アイテム
        $super_value = $this->super_item_array($average_value);
        
        mt_srand();
        $rand_result = mt_rand(0, 100);
        if ($rand_result <= ($average_value['holy'])) $holy = true;
        mt_srand();
        $rand_result = mt_rand(0, 100);
        if ($rand_result <= ($average_value['wicked'])) $wicked = true;
        mt_srand();
        $rand_result = mt_rand(0, 200);
        if ($rand_result <= ($average_value['holy'] + $average_value['wicked'])) $super = true;
        
        $result = array();
        $rank = array();
        if ($holy) { $result[] = $holy_value; $rank[] = 'holy'; }
        if ($wicked) { $result[] = $dark_value; $rank[] = 'wicked'; }
        if ($super) { $result[] = $super_value; $rank[] = 'super'; }
        if (count($result) > 0)
        {
            mt_srand();
            $rand_result = mt_rand(0, count($result) - 1);
            $result = $result[$rand_result];
            $rank = $rank[$rand_result];
        }
        else
        {
            $result = $normal_value;
            $rank = 'normal';
        }
        $colors = commons::get_item_color($result, false);
        $result['color_l'] = $colors['light'];
        $result['color_d'] = $colors['dark'];
        
        $smarty->assign('compose_items', $result);
        $smarty->assign('compose_rank', $rank);
        $smarty->assign('lists', $lists);
    }
    public function complete($request)
    {
        var_dump($_POST);
    }
    
    private function get_item_array()
    {
        $value = array();
        $value["scarcity"] = 0;
        $value["rigidity"] = 0;
        $value["size"] = 0;
        $value["weight"] = 0;
        $value["toxicity"] = 0;
        $value["naturally"] = 0;
        $value["edibility"] = 0;
        $value["animality"] = 0;
        $value["holy"] = 0;
        $value["wicked"] = 0;
        return $value;
    }
    
    private function sum_item_array($to, $from)
    {
        $to["scarcity"] += $from["scarcity"];
        $to["rigidity"] += $from["rigidity"];
        $to["size"] += $from["size"];
        $to["weight"] += $from["weight"];
        $to["toxicity"] += $from["toxicity"];
        $to["naturally"] += $from["naturally"];
        $to["edibility"] += $from["edibility"];
        $to["animality"] += $from["animality"];
        $to["holy"] += $from["holy"];
        $to["wicked"] += $from["wicked"];
        return $to;
    }
    
    private function average_item_array($item, $count)
    {
        $item["scarcity"] = $item["scarcity"] / $count;
        $item["rigidity"] = $item["rigidity"] / $count;
        $item["size"] = $item["size"] / $count;
        $item["weight"] = $item["weight"] / $count;
        $item["toxicity"] = $item["toxicity"] / $count;
        $item["naturally"] = $item["naturally"] / $count;
        $item["edibility"] = $item["edibility"] / $count;
        $item["animality"] = $item["animality"] / $count;
        $item["holy"] = $item["holy"] / $count;
        $item["wicked"] = $item["wicked"] / $count;
        return $item;
    }
    
    private function normal_item_array($item)
    {
        $item["scarcity"] = $item["scarcity"] * 1.1;
        $item["rigidity"] = $item["rigidity"] * 0.9;
        $item["size"] = $item["size"] * 1.1;
        $item["weight"] = $item["weight"] * 1.1;
        $item["toxicity"] = $item["toxicity"] * 0.9;
        $item["naturally"] = $item["naturally"] * 0.9;
        $item["edibility"] = $item["edibility"] * 1.1;
        $item["animality"] = $item["animality"] * 0.9;
        $item["holy"] = $item["holy"] * 0.9;
        $item["wicked"] = $item["wicked"] * 0.9;
        return $item;
    }
    
    private function holy_item_array($item)
    {
        $item["scarcity"] = $item["scarcity"] * 1.5;
        $item["rigidity"] = $item["rigidity"] * 1.5;
        $item["size"] = $item["size"] * 0.5;
        $item["weight"] = $item["weight"] * 0.5;
        $item["toxicity"] = $item["toxicity"] * 0.5;
        $item["naturally"] = $item["naturally"] * 0.9;
        $item["edibility"] = $item["edibility"] * 1.1;
        $item["animality"] = $item["animality"] * 0.9;
        $item["holy"] = $item["holy"] * 0.5;
        $item["wicked"] = $item["wicked"] * 0.9;
        return $item;
    }
    
    private function dark_item_array($item)
    {
        $item["scarcity"] = $item["scarcity"] * 1.5;
        $item["rigidity"] = $item["rigidity"] * 1.5;
        $item["size"] = $item["size"] * 1.1;
        $item["weight"] = $item["weight"] * 1.1;
        $item["toxicity"] = $item["toxicity"] * 1.5;
        $item["naturally"] = $item["naturally"] * 0.9;
        $item["edibility"] = $item["edibility"] * 1.1;
        $item["animality"] = $item["animality"] * 1.5;
        $item["holy"] = $item["holy"] * 0.9;
        $item["wicked"] = $item["wicked"] * 0.5;
        return $item;
    }
    
    private function super_item_array($item)
    {
        $item["scarcity"] = $item["scarcity"] * 2;
        $item["rigidity"] = $item["rigidity"] * 1.5;
        $item["size"] = $item["size"] * 0.5;
        $item["weight"] = $item["weight"] * 0.5;
        $item["toxicity"] = $item["toxicity"] * 0.5;
        $item["naturally"] = $item["naturally"] * 0.9;
        $item["edibility"] = $item["edibility"] * 1.1;
        $item["animality"] = $item["animality"] * 1.5;
        $item["holy"] = $item["holy"] * 0.5;
        $item["wicked"] = $item["wicked"] * 0.5;
        return $item;
    }
}
?>