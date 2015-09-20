<?php
// include
require_once APP_COMMON . 'Controller.php';	// コントローラー基本クラス

// DAO
require_once APP_MODEL . 'UserDao.php';		// [DAO] ユーザーテーブル

/**
 * WEB最初の登竜門
 *
 * @author Navi
 * @version 1.0.0
 */
class IndexController extends Base_Controller_Action
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
		try {
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
			$this->view->count = $user_dao->count(array('ids' => 0));
		}
		catch(Zend_Exception $e) {
			// エラーの処理

			// レンダリング(ビューの自動表示)をOFF
			$this->_helper->viewRenderer->setNoRender();
			$this->getResponse()->setHeader('Content-Type', 'text/plain');

			echo "error\n\n";
			echo $e->getMessage();
		}
	}

	/**
	 * アクションの名前
	 */
	public function inputAction() {
		try {
			// リクエスト処理

			// POSTから入力データを取得
			$input = Util::getPost($this->_request, array(
				'id'       => "",
				'password' => "templete",
				'date'     => Zend_Date::now()->toString("MM.dd.yyyy"),
				'time'     => Zend_Date::now()->toString("HH:mm:ss")
			));


			// DAOからの読み込み

			// Userテーブルから読み込み
			$user_dao = new UserDao();	/* DAOクラスの変数名はクラス名を全て小文字にしスネークケースにしてください */
			$user_data = $user_dao->findRow(array('user_id=?' => "vagrant", 'password=?' => "vagrant"));


			// 入力チェック

			// エラー変数の初期化
			$error = Util::cloneArrayKey($input);

			// 入力状態の確認
			if (Util::getInputStatus($this->_request)) {
				// ID
				$error['id'] = Util::check($input['id'], array(
					// 空のチェック
					array("empty", "IDが入力されていません"),
					// データベースと比較
					array("equal", "IDが正しくありません", $user_data['id'])
				));

				// Password
				$error['password'] = Util::check($input['password'], array(
					// 空のチェック
					array("empty", "パスワードが入力されていません"),
					// データベースと比較
					array("equal", "パスワードが正しくありません", $user_data['password'])
				));

				// 日付
				$error['date'] = Util::check($input['date'], array(
					// 空のチェック
					array("empty", "日付が入力されていません"),
					// 日付の妥当性
					array("date", "日付が正しくありません", "yyyy/MM/dd")
				));

				// 時間
				$error['time'] = Util::check($input['time'], array(
					// 空のチェック
					array("empty", "日付が入力されていません"),
					// 時間の妥当性
					array("date", "時間が正しくありません", "HH:mm:ss")
				));
			}


			// View関連

			// Viewへの値を渡す
			$this->view->input = $input;
			$this->view->error = $error;


			// Debug

			// レンダリング(ビューの自動表示)をOFF
			$this->_helper->viewRenderer->setNoRender();
			$this->getResponse()->setHeader('Content-Type', 'text/plain');

			print_r($input);
			print_r($error);
		}
		catch (Zend_Exception $e) {
			// エラーの処理
		}
	}
}