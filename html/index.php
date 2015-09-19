<?php
define('APP', dirname(dirname(__FILE__)) . "/application/");	// アプリケーションディレクトリへのパスを定義

require_once 'Zend/Config/Ini.php';							// ZendFramework設定ファイルリーダー読み込み
require_once 'Zend/Controller/Front.php';					// ZendFrameworkフロントコントローラスクリプト読み込み
require_once 'Zend/Controller/Action.php';					// ZendFrameworkコントローラー読み込み
require_once APP . '/configs/path.php';						// パス設定読み込み

Zend_Controller_Front::run(APP . "controllers");			// アクションコントローラの呼び出し
