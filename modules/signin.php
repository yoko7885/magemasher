<?php
class signin extends base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    public function start()
    {
        $smarty = $this->get_smarty();
        $smarty->assign('as', utils::crypt('signin'));
        $smarty->assign('do', utils::crypt('execute'));
    }
    
    public function execute()
    {
        $email = $_POST['inputEmail'];
        $password = $_POST['inputPassword'];
        $smarty = $this->get_smarty();
        
        $account = $this->get_account($email, $password);
        
        if (count($account) <= 0)
        {
            $smarty->assign('error', 'account not found or incorrect password');
            return;
        }
        $_SESSION['MGM_ACCOUNT_ID'] = $account[0]['account_id'];
        
        $this->next_access = utils::crypt('compose');
        $this->next_method = null;
    }
    
    private function get_account($email, $password)
    {
        $result = $this->db->select
        (
            array
            (
                'table' => 'account', 
                'where' => array
                (
                    "login_id = \"{$email}\""
                    , "and"
                    , "password = \"{$password}\""
                    , "and"
                    , "invalid_flg != 1"
                )
            )
        );
        return $result;
    }
}
?>