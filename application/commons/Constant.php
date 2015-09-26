<?php
// タイムゾーン
date_default_timezone_set("Asia/Tokyo");

// 入力の判定名
define('FORM_KEY', "_status");
// 入力の状態
define('FORM_NONE', "0");	// 未入力
define('FORM_INPUT', "1");	// 入力

// 日付のフォーマット
define('FORMAT_DATE', "yyyy/MM/dd");
// 時間のフォーマット
define('FORMAT_TIME', "HH:mm:ss");

// 認証失敗時のリダイレクト先
define('AUTH_NOT_REDIRECT', "/");