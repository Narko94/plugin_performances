<?php
if (!defined('IN_CMS')) { exit(); }

/* Делаем страницу заявки дефолтной для пользователя заявок */
if (!in_array('page_view', AuthUser::getPermissions()) && AuthUser::hasPermission('performances')) {
	
	$custom_user_url_a_m = '/admin/plugin/performances';
	$current_url = $_SERVER['REQUEST_URI'];
	$from_urls = array('/admin', '/admin/page');
	
	/* Убираем таб Page из меню */
	/* Для корректной работы админки плагина необходимо включить внутренние запросы в массив: */
	$in_requests_a_m = array('/performances/ajax', '/performances/performances/');
	
	$in_requests = isset($in_requests) ? array_merge($in_requests, $in_requests_a_m) : $in_requests_a_m;
	
	$close_tab = true;
	foreach ($in_requests as $in) {
		if (stripos($current_url, $in)!==false && key(array_slice(AuthUser::getPermissions(),-1,1,true)) == 'performances') {
			$close_tab = false;
			break;
		}
	}
	
	if ($close_tab && preg_match('/admin/i', $current_url)) {
		?>
		<script>
			document.addEventListener('DOMContentLoaded', function(){
				$('#mainTabs').find('#page-plugin').hide();
			});
		</script>
		<?php
	}
	
	if (in_array($current_url, $from_urls) && $current_url != $custom_user_url_a_m) redirect($custom_user_url_a_m);
}
/* /Делаем страницу заявки дефолтной для пользователя заявок */

if(!defined("PERFORMANCES_ROOT")) define('PERFORMANCES_ROOT', __DIR__);
if(!defined("PERFORMANCES_CLASSES")) define('PERFORMANCES_CLASSES', PERFORMANCES_ROOT . DS .'classes');
if(!defined("PERFORMANCES_PUBLIC")) define("PERFORMANCES_PUBLIC", CMS_ROOT . DS . "public/performances");

if(!defined("PERFORMANCES_DSN")) define('PERFORMANCES_DSN',     'sqlite:' . PERFORMANCES_ROOT . DS . 'data' . DS . 'data.db');
if(!defined("PERFORMANCES_DB_USER")) define('PERFORMANCES_DB_USER', 'root');
if(!defined("PERFORMANCES_DB_PASS")) define('PERFORMANCES_DB_PASS', '');

Plugin::setInfos(array(
    'id'                    => 'performances',
    'title'                 => 'PERFORMANCES',
    'description'           => 'Плагин для управления спектаклями',
    'license'               => 'Unlicense',
    'website'               => 'http://art3d.ru',
    'version'               => '1.0.0',
    'require_wolf_version'  => '0.8.1',
    'type'                  => 'both',
    'author'                => 'Валаханович Антон'
));

AutoLoader::addFolder([PERFORMANCES_CLASSES]);

Plugin::addController('performances', 'Спектакли', 'performances', true);

Dispatcher::addRoute('/performances/ajax','/plugin/performances/ajax');
Dispatcher::addRoute('/spektakli/:any','/plugin/performances/getPage/$1');
Dispatcher::addRoute('/performances/uploading/:any/:any/:num/:num','/plugin/performances/uploading/$1/$2/$3/$4');