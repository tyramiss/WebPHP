<?php
// 設定の初期化
$_CONFIG = array();

// 設定から読み込み
function config($name, $value = null) {
	global $_CONFIG;

	// 文字列ではない
	if (!is_string($name)) {
		throw new Exception('$name params not type string');
	}
	// 配列にない
	if (!isset($_CONFG[$name])) {
		throw new Exception('$name params not keys');
	}

	// データ登録
	if ($value !== null) {
		$_CONFG[$name] = $value;
	}

	// 内容読み込み
	return($_CONFIG[$name]);
}