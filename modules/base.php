<?php
abstract class base
{
    public static $smarty;
    public $db;
    
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
        if (!self::$smarty)
        {
            require('./modules/smarty-3.1.27/libs/Smarty.class.php');
            self::$smarty = new Smarty();
    
            // $smarty->setTemplateDir('/web/www.example.com/smarty/templates');
            self::$smarty->setCompileDir('./temp');
            // $smarty->setCacheDir('/web/www.example.com/smarty/cache');
            // $smarty->setConfigDir('/web/www.example.com/smarty/configs');
        }
        self::$smarty->assign($_POST);
    }
    abstract public function start();
    
    public function set_db()
    {
        $this->db = new txtSQL('./data');
        $this->db->connect('root', '');
        $this->db->selectdb('magemasher');
    }
    public function unset_db()
    {
        $this->db->disconnect();
    }
}
?>