<?php
// エラー関連
include CORE_DIR . "/lib/error.php";

// ハッシュ
include CORE_DIR . "/lib/hash.php";
// 環境設定
include CORE_DIR . "/lib/env.php";

// ディレクトリ
include CORE_DIR . "/lib/dir.php";
// ファイル管理
include CORE_DIR . "/lib/file.php";

// システム設定ファイルの読み込み
include CORE_DIR . "/config.php";

// フレームワーク
include CORE_DIR . "/lib/frame.php";
include CORE_DIR . "/lib/view.php";

try {
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
		if (baseFrame::isControl($control)) {
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
	if (!baseFrame::isControl($control)) {
		throw new NotFoundException("コントローラーにファイルが存在しません。\nFile : " . baseFrame::getControlPath($control));
	}

	// ユーザー設定
	if (!File::isRead(CONFIG_DIR . "/config" . PHP_EXTENSION)) {
		include CONFIG_DIR . "/config" . PHP_EXTENSION;
	}

	// コントローラーの読み込み
	include baseFrame::getControlPath($control);

	// クラス作成
	$frame = new controlFrame($control, $param);
}
// 404 Not Found
catch(NotFoundException $error) {
		// 404ファイルが存在
		$filepath = VIEW_DIR . "/" . VIEW_ERROR_DIR . "/404" . CTP_EXTENSION;
		if (File::isRead($filepath)) {
			include $filepath;
		}
		else {
			include CORE_DIR . "/view/error/404" . CTP_EXTENSION;
		}
}