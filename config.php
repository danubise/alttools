<?php


/**
 * Created by Unix develop team.
 * User: vlad
 * Date: 19.02.15
 * Time: 21:39
 */

///////////////////////////////////////////////////////////////
/**
 * SYSTEM CONFIG START
 */
///////////////////////////////////////////////////////////////
$DEBUG = TRUE; //Выключаем режим дебага

$system_path = 'system';

$application_folder = 'application';

$pub_folder = 'pub';

$core_dir = 'tools';

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
define('COREPATH', __DIR__.'/');
define('BASEPATH', COREPATH.$system_path.'/');
define('APPPATH', COREPATH.$application_folder.'/');
define('EXT', '.php');
define('libs', APPPATH.'libs/');
define('models', APPPATH.'models/');
define('config', APPPATH.'data/');
define('layout', APPPATH.'views/layout/');
define('cron', APPPATH.'cron/');
define('SITE_TITLE', '');

/**
 * Mysql db config
 */
define('db_lib', libs.'mysql.php');
$_config['mysql'] = array(
    'host' => 'localhost',
    'user' => 'tools',
    'password' => 'tools',
    'base' => 'tools'
);

$_config_CDR['mysql'] = array(
    'host' => 'localhost',
    'user' => 'freepbxuser',
    'password' => 'f3c2b8bd0fa891e5ddeb040a9983148e',
    'base' => 'asteriskcdrdb'
);

///////////////////////////////////////////////////////////////
/**
 *
 *  SYSTEM CONFIG END
 *
 */
///////////////////////////////////////////////////////////////
