<?php
// 環境設定
$_ENV = $_SERVER;
$_ENV['SSL'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on";
$_ENV['PROTOCOL'] = $_ENV['SSL'] ? "https://" : "http://";
$_ENV['ROOT'] = substr($_SERVER['REDIRECT_URL'], 0, strpos($_SERVER['REDIRECT_URL'], 'web_root/'));
$_ENV['PARAM'] = $_GET['path'];

// 環境設定の呼び出し
function env($name) {
	global $_ENV;
	return($_ENV[$name]);
}