<?php
// ディレクトリパス
define('WEB_ROOT_DIR', dirname(__FILE__));
define('APP_DIR', dirname(WEB_ROOT_DIR));
define('CORE_DIR', APP_DIR . "/core");
define('CONFIG_DIR', APP_DIR . "/config");
define('CONTROLL_DIR', APP_DIR . "/control");
define('TABLE_DIR', APP_DIR . "/table");
define('FORM_DIR', APP_DIR . "/form");
define('VIEW_DIR', APP_DIR . "/view");
define('TMP_DIR', APP_DIR . "/tmp");


// パスが未設定の場合のデフォルト値
if (!isset($_GET['path'])) {
	$_GET['path'] = "";
}

include CORE_DIR . "/core.php";
