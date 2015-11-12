<?php
class top extends base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    public function start()
    {
        $account_id = $_SESSION['MGM_ACCOUNT_ID'];
        $user = $this->get_user($account_id)[0];
        
        $smarty = $this->get_smarty();
        $smarty->assign('user', $user);
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
}
?>