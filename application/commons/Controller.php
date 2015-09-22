<?php
// include
require_once 'Zend/Auth.php';					// ZendFrameworkの認証読み込み
require_once 'Zend/Auth/Adapter/DbTable.php';	// ZendFrameworkのデータベース認証読み込み
require_once 'Zend/Controller/Action.php';		// ZendFrameworkコントローラー読み込み
require_once APP_COMMON . 'Utility.php';		// ユーティリティークラス
require_once APP_COMMON . 'Check.php';			// 入力チェッククラス
require_once APP_COMMON . 'ViewSupport.php';	// 表示サポート

/**
 * コントローラーの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
abstract class Base_Controller_Action extends Zend_Controller_Action
{
	// すべてのコントローラーで共通して使用する処理を記述する
}