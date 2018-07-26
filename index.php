<?php
session_start();

define ('VERSION', '1.0.0.0001'. date("YmdHis"));

require_once('./modules/base.php');
require_once('./modules/commons.php');
require_once('./modules/myjson.php');

require_once('./modules/utils.php');
// echo utils::crypt('top');//KOdyhZFvN7g%3D
// echo utils::crypt('request');//dYHtB9ANEcg%3D
// echo utils::crypt('search');//Qh36TRW6HtA%3D

define ('TXTSQL_CORE_PATH', './modules/');
define ('TXTSQL_PARSER_PATH', './modules/');
require_once('./modules/txtSQL.class.php');

define ('ROOT_PATH', dirname(__FILE__));

$loopcnt = 0;

do
{
    $loopcnt++;
    $name = 'signin';
    if (array_key_exists('as', $_REQUEST))
    {
        if ($_REQUEST['as']) $name = utils::plain($_REQUEST['as']);
    }

    $m_file = './modules/'.$name.'.php';
    if (file_exists($m_file))
    {
        require_once($m_file);
        $class = new $name();
        $do = 'start';
        if (array_key_exists('do', $_REQUEST))
        {
            if ($_REQUEST['do']) $do = utils::plain($_REQUEST['do']);
        }
        $class->$do($_REQUEST);
        $smarty = $class->get_smarty();
        if ($class->next_access)
        {
            $_REQUEST['as'] = $class->next_access;
            $_REQUEST['do'] = $class->next_method;
            $class = null;
            continue;
        }
    }
    break;
}
while(1);

$v_file = null;
if (file_exists('./view/'.$name.'/'.$do.'.tpl'))
{
    $v_file = './view/'.$name.'/'.$do.'.tpl';
}
else
{
    if (file_exists('./view/'.$name.'/index.tpl'))
    {
        $v_file = './view/'.$name.'/index.tpl';
    }
}
if ($v_file)
{
    if ($smarty)
    {
        $html = $smarty->fetch($v_file);
    }
    else
    {
        $html = file_get_contents($v_file);
    }
}

echo $html;

?>