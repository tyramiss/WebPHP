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

	// 渡されたパスの分解
	$paths = preg_split('|/|', trim($_GET['path'], "/"));

	// イテレータ
	$i = 0;
	$end = count($paths);

	// コントローラーファイルの検索
	$control = "";
	while ($i < $end) {
		$path = $paths[$i++];
		if (!$path) {
			$path = "index";
		}
		$control .= $path;
		if (File::isRead(CONTROLL_DIR . "/" . $control . PHP_EXTENSION)) {
			break;
		}
		$control .= "/";
	}

	// パラメーター部分の取得
	$param = array();
	while (isset($paths[$i])) {
		$param[] = $paths[$i++];
	}

	// ユーザーのルート設定がある場合は従う
	if (File::isRead(CONFIG_DIR . "/route.php")) {
		include CONFIG_DIR . "/route.php";
	}

	// 404 Not Found
	if (!File::isRead(CONTROLL_DIR . "/" . $control . PHP_EXTENSION)) {
		throw new FileNotFoundException();
	}

	// コントローラーの読み込み
	include CONTROLL_DIR . "/" . $control . PHP_EXTENSION;

	// クラス作成
	$frame = new controlFrame($control, $param);
}
// 404 Not Found
catch(FileNotFoundException $err) {
		// ヘッダー
		header("HTTP/1.1 404 Not Found");

		// 404ファイルが存在
		$filepath = VIEW_DIR . "/error/404" . CTP_EXTENSION;
		if (File::isRead($filepath)) {
			include $filepath;
		}
		else {
			include CORE_DIR . "/view/error/404" . CTP_EXTENSION;
		}
}