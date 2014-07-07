<?php
// ファイル管理クラス
include CORE_DIR . "/lib/file.php";

// システム設定ファイルの読み込み
include CORE_DIR . "config.php";

// イテレータ
$i = 0;

// 渡されたパスの分解
$paths = preg_split('|/|', $_GET['path']);

// ルート設定値の初期化
$_ROUTE = array(
	'theme'   => "",
	'page'    => "page",
	'layout'  => "default",
	'control' => "",
	'param'   => array()
);

// コントローラーファイルの選定
while (isset($paths[$i])) {
	$path = $paths[$i++];
	if (!$path) {
		$path = "index";
	}
	$_ROUTE['control'] .= "/" . $path;
	if (File::isRead(CONTROLL_DIR . $_ROUTE['control'] . PHP_EXTENSION)) {
		break;
	}
}

// パラメーター部分の取得
while (isset($paths[$i])) {
	$_ROUTE['param'][] = $paths[$i++];
}

// ユーザーのルート設定がある場合は従う
if (File::isRead(CONFIG_DIR . "/route.php")) {
	include CONFIG_DIR . "/route.php";
}

// 404 Not Found
if (!File::isRead(CONTROLL_DIR . $_ROUTE['control'] . PHP_EXTENSION)) {
	$_ROUTE['page'] = "error";
	$_ROUTE['layout'] = "error";
	$_ROUTE['control'] = "404";
}

header("Content-Type: text/plain; charset=UTF-8");
print_r($_SERVER);
exit;

// ベースURL
$_URL = $_SERVER;
$_URL['SSL'] = $_URL['HTTPS'] === "https";
$_URL['PROTOCOL'] = $_URL['SSL'] ? "https://" : "http://";
$_URL['ROOT'] = substr($_URL['REQUEST_URI'], 0, strpos($_URL['REQUEST_URI'], '/web/'));
$_URL['PARAM'] = $_GET['path'];
