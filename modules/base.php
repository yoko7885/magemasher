<?php

abstract class base
{
    /**
     * スマーティクラス
     * @var Smarty
     */
    public static $smarty;

    /**
     * txtSQLクラス
     * @var txtSQL
     */
    public $db;

    /**
     * txtSQLクラス（ユーザースキーマー）
     * @var txtSQL
     */
    public $user_db;

    public $next_access = null;

    public $next_method = null;

    public function __construct($class)
    {
        $this->set_smarty();
        $this->set_db();
    }

    public function __destruct()
    {
        $this->unset_db();
    }

    public function get_smarty()
    {
        return self::$smarty;
    }

    public function set_smarty()
    {
        if (! self::$smarty) {
            require ('./modules/smarty-3.1.27/libs/Smarty.class.php');
            self::$smarty = new Smarty();

            self::$smarty->setCompileDir('./temp');
        }
        self::$smarty->assign($_POST);
    }

    abstract public function start($request);

    public function set_db()
    {
        $this->db = new txtSQL('./data');
        $this->db->connect('root', '');
        $this->db->selectdb('magemasher');

        if ($_SESSION['MGM_ACCOUNT_ID']) {
            $this->user_db = new txtSQL('./data');
            $this->user_db->connect('root', '');
            $this->user_db->selectdb('account_' . $_SESSION['MGM_ACCOUNT_ID']);
        }
    }

    public function unset_db()
    {
        $this->db->disconnect();
        $this->user_db->disconnect();
    }
}
?>