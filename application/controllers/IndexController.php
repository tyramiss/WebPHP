<?php
// include
require_once APP_MODEL . 'UserDao.php';

/**
 * WEB最初の登竜門
 *
 * @author Navi
 * @version 1.0.0
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * 初期化
	 */
	public function init() {
	}

	/**
	 * 共通アクション前関数
	 */
	public function preDispatch() {
	}

	/**
	 * サイト入り口
	 */
	public function indexAction() {
		// ユーザーテーブルの読み込み
		$user_dao = new UserDao();
		// $ins = $user_dao->insert(array(
		// 	'user_id' => "test2",
		// 	'password' => "tset2",
		// 	'name' => "Test2"
		// ));
		// echo $ins , "\n";
		// $del = $user_dao->delete(array('user_id' => "test"));
		// echo $del , "\n";
		// $upd = $user_dao->update(array('user_id' => "vagrant"));
		// echo $upd , "\n";
		$this->view->user = $user_dao->find();
echo $user_dao->count() , "\n";
print_r($this->view->user->toArray());
exit();
	}
}