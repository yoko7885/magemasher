<?php
$CFG = array();

// Path to the txtSQL.class.php file
$CFG['txtsql']['class']     = '../../modules/txtSQL.class.php';

// Path to the directory containing 'txtSQL.core.php'
$CFG['txtsql']['core_path'] = '../../modules/';

// Path to the directory containing the txtSQL databases
$CFG['txtsql']['data_path'] = '../../data';

include('./startup.php');
?>