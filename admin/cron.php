<?php
define('VERSION', '3.0.2.0');

if (!ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

require_once(__DIR__ . '/config.php');

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

$_SERVER['HTTP_HOST'] = HTTP_CATALOG;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir(DIR_APPLICATION);

// Check if SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Modification Override
function modification($filename) {
	if (defined('DIR_CATALOG')) {
		$file = DIR_MODIFICATION . 'admin/' .  substr($filename, strlen(DIR_APPLICATION));
	} elseif (defined('DIR_OPENCART')) {
		$file = DIR_MODIFICATION . 'install/' .  substr($filename, strlen(DIR_APPLICATION));
	} else {
		$file = DIR_MODIFICATION . 'catalog/' . substr($filename, strlen(DIR_APPLICATION));
	}

	if (substr($filename, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
		$file = DIR_MODIFICATION . 'system/' . substr($filename, strlen(DIR_SYSTEM));
	}

	if (is_file($file)) {
		return $file;
	}

	return $filename;
}

function library($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));

		return true;
	} else {
		return false;
	}
}

spl_autoload_register('library');
spl_autoload_extensions('.php');

// Engine
require_once(modification(DIR_SYSTEM . 'engine/action.php'));
require_once(modification(DIR_SYSTEM . 'engine/controller.php'));
require_once(modification(DIR_SYSTEM . 'engine/event.php'));
require_once(modification(DIR_SYSTEM . 'engine/router.php'));
require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
require_once(modification(DIR_SYSTEM . 'engine/model.php'));
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));
require_once(modification(DIR_SYSTEM . 'engine/proxy.php'));

// Helper
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');		

$fh = fopen(__FILE__, 'r');
if (!flock($fh, LOCK_EX | LOCK_NB)) exit;

class Cron {
	private $task = array();
	
	public function __construct() {
		$autor = 'usergio';
	}

	public function __get($name) {
		return $this->registry->get($name);
	}
	
	public function run($registry, $task, $param) {
		$this->registry = $registry;		
		$this->load->controller($task, $param);	

		return;
	}
}

$cron = new Cron;

// Registry
$registry = new Registry();

// Config
$config = new Config();
$config->load('default');
$config->load('admin');
$registry->set('config', $config);


// Log
$log = new Log($config->get('error_filename'));
$registry->set('log', $log);

date_default_timezone_set($config->get('date_timezone'));

function error_handler($message) {
		global $log, $config;

		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}	
		if (strlen($message) > 5) $log->write($message);

		return true;
	}

set_error_handler('error_handler');

// Event
$event = new Event($registry);
$registry->set('event', $event);

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $value) {
		foreach ($value as $priority => $action) {
			$event->register($key, new Action($action), $priority);
		}
	}
}

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Request
$registry->set('request', new Request());

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Database
if ($config->get('db_autostart')) {
	$registry->set('db', new DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port')));
}

// Session
$session = new Session($config->get('session_engine'), $registry);
$registry->set('session', $session);
$session->start('cronssessionconstant1value');	
$session->data['token'] = 'token0for0cron0constant100value0';

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// $db
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], json_decode($setting['value'], true));
	}
}

// Language
$languages = array();
$query = $db->query("SELECT * FROM " . DB_PREFIX . "language"); 

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);	
$registry->set('language', $language); 		

if ($config->has('language_autoload')) {
	foreach ($config->get('language_autoload') as $value) {
		$loader->language($value);
	}
}

// Route
$route = new Router($registry);

/***************************  ERRORS  ***************************************/
    function mess($err, $file) {
		switch ($err) {			
			case 1:	$message = 'FTP: ivalid file extension';		
			break;
			case 2:	$message = 'FTP: failed FTP connect';			
			break;
			case 3:	$message = 'FTP: file "' . $file . '" not found';			
			break;
			case 4:	$message = 'LINK: ivalid file extension';		
			break;
			case 5:	$message = 'LINK: can not create file in uploads';			
			break;
			case 6:	$message = 'LINK: file "' . $file . '" not found';			
			break;		
		}
		if ($err) {
			error_handler($message);
			$file_er    = "./uploads/errors.tmp";
			$er = @fopen($file_er,'a');
			if (!$er) $er = @fopen($file_er,'w+');
			@fputs($er, $message ." \n");
			@fclose($er);
			return;
		}	
	}
	
/**************************  FTP  ********************************************/
	function get_ftp($form_id, $file_source, $ftp_server, $user_name, $user_pass, $ext) {
		if (empty($ext)) return 1;		
		$ext = '.' . $ext;	
		$local_file = DIR_APPLICATION . 'uploads/' . $form_id . $ext;
	
		// set up basic connection	
		$conn_id = ftp_connect($ftp_server);

		// login with username and password	
		$login_result = ftp_login($conn_id, $user_name, $user_pass);
		if (!$login_result) return 2;
		ftp_pasv($conn_id, true);

		// try to download $server_file and save to $local_file
		if (!ftp_get($conn_id, $local_file, $file_source, FTP_BINARY)) return 3;
	
		ftp_close($conn_id);
		return 0;
	} 

/***************************  LINK  *****************************************/
	function get_link($url, $form_id, $ext) {
		if (empty($ext)) return 4;
		$ext = '.' . $ext;		
		$local_file = DIR_APPLICATION . 'uploads/' . $form_id . $ext;
		if (file_exists($local_file)) unlink($local_file);
	/*	$fp = @fopen($local_file,'wb');			
		if (!$fp) return 5;
	
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);	
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		$fp = @fopen($local_file,'r');
		if (!$fp) return 6;
		$a = fread($fp, 20);
		if (substr_count($a, '!DOCTYPE')) {
			fclose($fp);
			return 6;
		}	
		fclose($fp); */
	
		$cc = '/usr/bin/wget -c -t 1 -O uploads/' . $form_id . $ext . ' "' . $url . '"';
		$cc = str_replace('&amp;', '&', $cc);
		exec($cc); 	

		return 0;
	}
	
/**************************  END LINK ***************************************/

	$query = $db->query("SELECT * FROM " . DB_PREFIX . "suppler_cron ORDER BY `nom_id`, csort ASC");
	
	if (empty($query->rows)) exit;	
	
	foreach ($query->rows as $row) {
		
		$param = array();	
		$mk = time();
		if (substr_count($row['port1'], '+')) {
			$a = substr($row['port1'], 1);
			$mk = time() + (int)$a*3600;
		}
		if (substr_count($row['port1'], '-')) {
			$a = substr($row['port1'], 1);
			$mk = time() - (int)$a*3600;
		}					
		$period = 240; // pause 240 minutes  = 4 hours
			
		if (($mk-$row['cron_status'])/60 < $period or !$row['on_off'])  continue;
		
		$h = date('G', $mk);
		$d = date('N', $mk);
		$w = date('j', $mk);
		$w = ceil($w/7);

		if ($row['text'] != '*' and $h != (int)$row['text']) continue;
		if ($row['text1'] != '*' and $d != (int)$row['text1']) continue;
		if ($row['text2'] != '*' and $w != (int)$row['text2']) continue;
		
		$param['form_id'] = $row['form_id'];
		$param['command'] = $row['cmd'];
		$param['ext'] = 'xls';
		if ($row['ext'] == 2) $param['ext'] = 'xml';
		if ($row['ext'] == 3) $param['ext'] = 'csv';
		
		$param['act_find'] = $row['act_find'];	
		$param['act_change'] = $row['act_change'];
		$param['all'] = $row['all0'];
		$param['isno'] = $row['isno'];
		$param['pr_name'] = $row['pr_name'];
		$param['link'] = $row['link'];
		
		$flag = 0;
		if (!empty($row['link']) and !substr_count($row['link'], 'http') and !substr_count($row['link'], 'www') and !substr_count($row['link'], "/") and !empty($row['ftp_name']) and !empty($row['ftp_pass']) and $row['task'] == 4) {

			$message ='';
			$flag = 1;
			if ($err = get_ftp($row['form_id'], $row['pr_name'], $row['link'], $row['ftp_name'], $row['ftp_pass'], $param['ext'])) {
				$flag = 2;
				mess($err, $row['pr_name']);
			}
			
		}
		if (!empty($row['link']) and (substr_count($row['link'], 'http') or substr_count($row['link'], 'www') or substr_count($row['link'], "/")) and  $row['task'] == 4) {
			
			$message ='';
			$flag = 1;
			if ($err = get_link($row['link'], $row['form_id'], $param['ext'])) {
				$flag = 2;
				mess($err, $row['link']);
			}
			
		}
		
		$path = "./uploads/" . $param['pr_name'];	
	
		if ($row['task'] == 4 and !$flag) {	
			if (!file_exists($path)) continue;
			else $flag = 1;
		}
		
		$task = "catalog/suppler/cronAction";
		if ($row['task'] == 4) $task = "catalog/suppler/cronLoadfile";
		
		if (!empty($param['command']) or ($row['task'] == 4 and $flag == 1)) {			
			
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `go` = '" . 1 . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
			
			if ($row['task'] == 4)
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `errors` = '" . 0 . "', `report` = '" . 0 . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
		
			$cron->run($registry, $task, $param);
			
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `cron_status` = '" . $mk . "', `go` = '" . 0 . "', `save_form` = '" . $mk . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");

		}
		
		if ($flag == 2 and $row['task'] == 4) {
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `cron_status` = '" . $mk . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
		}		
		
		if ($row['task'] == 4) {		
			$errors = 0;
			$report = 0;
			$path_err = DIR_APPLICATION . 'uploads/errors.tmp';
			$path_rep = DIR_APPLICATION . 'uploads/report.tmp';
			if (file_exists($path_err)) $errors = sizeof(file($path_err));
			if (file_exists($path_rep)) $report = sizeof(file($path_rep));

			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `errors` = '" . $errors . "', `report` = '" . $report . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");	
		
		}	
		
		if (!empty($row['mail']) and $row['task'] == 4) {

			$text = 'Price-list not found';
			if (!empty($_SESSION["e-r-r"]) and $flag != 2) {
				$errs = explode(";", $_SESSION["e-r-r"]);
				$err = $errs[0];
				if (!isset($errs[1])) $errs[1] = 0;
				if (!isset($errs[2])) $errs[2] = 0;
				
				$text = 'TOTAL: Products added ' . $errs[1] . ' Products updated ' . $errs[2];
			}
			
			$sos = 0;
			$path_err = DIR_APPLICATION . 'uploads/errors.tmp';
			$path_rep = DIR_APPLICATION . 'uploads/report.tmp';
			if (!file_exists($path_rep) and !file_exists($path_err)) $sos = 1;
			if (!file_exists($path_rep) and file_exists($path_err)) $sos = 2;
			if (file_exists($path_err) and file_exists($path_rep)) {	
				$lines_err = sizeof(file($path_err));
				$lines_rep = sizeof(file($path_rep));
				if ($lines_err !=0 and $lines_rep/$lines_err > 5) $sos = 3;
				if ($lines_err !=0 and $lines_rep/$lines_err > 10) $sos = 4;
				if ($lines_err !=0 and $lines_rep/$lines_err > 20) $sos = 5;
				if ($lines_err !=0 and $lines_rep/$lines_err > 50) $sos = 6;
			}
			if (file_exists($path_rep) and !file_exists($path_err)) $sos = 7;
			
			if ($flag == 2) $sos = 1;

			$form = ' Form number ' . $row['form_id'];
			
			$sub[0] = 'Hello! Message from Cron. ';
			$sub[1] = 'Good morning! ';
			$sub[2] = 'Hi! ';
			$sub[3] = 'Have a nice day! ';
			$sub[4] = 'Please, read me. ';
			$sub[5] = 'Good news. ';
			if ($sos < 3) $sub[5] = 'The news is not very good. ';
			$sub[6] = 'News. ';
			$sub[7] = 'This is not spam. ';
			$sub[8] = "Hello! I'm not tired of you? ";
			$sub[9] = 'Report. ';
			$sub[10] = 'Would you like to see the report? ';
			$sub[11] = 'Good news. There is a report. ';
			if ($sos < 3) $sub[11] = 'The news is not very good. There is a report. ';
			$sub[12] = 'Report? Here it is. ';
			$i = rand(0, 12);
			$greeting = $sub[$i];
			
			if ($sos == 1) {
				$b[0] = 'Job not done. See System -> Error Log';
				$b[1] = 'Nothing succeeded. See System -> Error Log';
				$b[2] = 'See System -> Error Log';
				$b[3] = 'All very bad. See System -> Error Log';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 2) {
				$b[0] = 'Job not done. Check supplier form settings.';
				$b[1] = 'Nothing succeeded. Check supplier form settings.';
				$b[2] = 'Only errors. Check supplier form settings.';
				$b[3] = 'All very bad. Check supplier form settings.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 3) {
				$b[0] = 'A lot of errors.';
				$b[1] = 'More than 20 percent errors.';
				$b[2] = 'Something went wrong, a lot of errors.';
				$b[3] = 'Too many errors. Check supplier form settings.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 4) {
				$b[0] = 'There are errors.';
				$b[1] = 'More than 10 percent errors.';
				$b[2] = 'Look at the error file.';
				$b[3] = 'Little reason for joy. There are errors.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 5) {
				$b[0] = 'There are some errors.';
				$b[1] = 'More than 5 percent errors.';
				$b[2] = 'Not quite good, best to see the error file.';
				$b[3] = 'Not bad.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 6) {
				$b[0] = 'Very few errors, but there are.';
				$b[1] = 'Less than 5 percent error.';
				$b[2] = 'Quite perfect.';
			}
			$i = rand(0, 2);
			$body = $b[$i];
			
			if ($sos == 7) {
				$b[0] = 'Great job. No errors';
				$b[1] = 'Perfect. No errors';
				$b[2] = 'No errors.';
			}
			$i = rand(0, 2);
			$body = $b[$i];
					
			$subject = $greeting . $body . $form;
		
			$mail = new Mail();
    
			$mail->setTo($row['mail']);
			$mail->setFrom('your.server');
			$mail->setSender('Plugin "Suppliers"');
			$mail->setSubject($subject);
			$mail->setText($text);
			if (($row['rtype'] == 2 or $sos< 5) and $sos != 1) $mail->addAttachment(DIR_APPLICATION . 'uploads/errors.tmp');
			if ($row['rtype'] == 3 and $sos != 1) $mail->addAttachment(DIR_APPLICATION . 'uploads/report.tmp');
			if ($row['rtype'] == 4 and $sos != 1) {
				$mail->addAttachment(DIR_APPLICATION . 'uploads/errors.tmp');
				$mail->addAttachment(DIR_APPLICATION . 'uploads/report.tmp');
			}
			$mail->send();
		}
	}
?>