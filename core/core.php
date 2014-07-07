<?php
// エラー関連
include CORE_DIR . "/lib/error.php";

// システム設定ファイルの読み込み
include CORE_DIR . "/config.php";

// ファイル管理
include CORE_DIR . "/lib/file.php";

// ハッシュ
include CORE_DIR . "/lib/hash.php";

try {
	// 環境設定
	$_ENV = $_SERVER;
	$_ENV['SSL'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on";
	$_ENV['PROTOCOL'] = $_ENV['SSL'] ? "https://" : "http://";
	$_ENV['ROOT'] = substr($_SERVER['REDIRECT_URL'], 0, strpos($_SERVER['REDIRECT_URL'], 'web_root/'));
	$_ENV['PARAM'] = $_GET['path'];

	// イテレータ
	$i = 0;

	// 渡されたパスの分解
	$paths = preg_split('|/|', trim($_GET['path'], "/"));

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
	$_ROUTE['control'] = trim($_ROUTE['control'], "/");

	// パラメーター部分の取得
	while (isset($paths[$i])) {
		$_ROUTE['param'][] = $paths[$i++];
	}

	// ユーザーのルート設定がある場合は従う
	if (File::isRead(CONFIG_DIR . "/route.php")) {
		include CONFIG_DIR . "/route.php";
	}

	// 404 Not Found
	if (!File::isRead(CONTROLL_DIR . "/" . $_ROUTE['control'] . PHP_EXTENSION)) {
		throw new FileNotFoundException();
	}

	// コントローラーの読み込み
	include CONTROLL_DIR . "/" . $_ROUTE['control'] . PHP_EXTENSION;

}
// 404 Not Found
catch(FileNotFoundException $err) {
		$_ROUTE['page'] = "error";
		$_ROUTE['layout'] = "error";
		$_ROUTE['control'] = "404";

		// ヘッダー
		header("HTTP/1.1 404 Not Found");

		// 404ファイルが存在
		$filepath = VIEW_DIR . "/" . $_ROUTE['page'] . $_ROUTE['control'] . CTP_EXTENSION;
		if (File::isRead($filepath)) {
			include $filepath;
		}
		else {
			include CORE_DIR . "/view/error" . $_ROUTE['control'] . CTP_EXTENSION;
		}
}