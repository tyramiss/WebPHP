<?php
// include
require_once 'Zend/Controller/Action.php';		// ZendFrameworkコントローラー読み込み
require_once APP_COMMON . 'Utility.php';		// ユーティリティークラス
require_once APP_COMMON . 'ViewSupport.php';	// 表示サポート

/**
 * コントローラーの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Base_Controller_Action extends Zend_Controller_Action
{
	// すべてのコントローラーで共通して使用する処理を記述する
}