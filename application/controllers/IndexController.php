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
	/** 入力エラー内容 */
	const ERR_USERID_EMPTY   = "ユーザーIDが入力されていません";
	const ERR_PASSWORD_EMPTY = "パスワードが入力されていません";
	const ERR_USER_EQUAL     = "ユーザーIDかパスワードが正しくありません";

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
	 * ロップページ/ログイン
	 */
	public function indexAction() {
		// データベース取得
		$db = Db::Connect();
		// トランザクション
		$db->beginTransaction();

		try {
			// すでにログインしている場合はログアウト
			Auth::logout();

			// POSTから入力データを取得
			$input = Util::getPost($this->getRequest(), array(
				'user_id'  => "",
				'password' => ""
			));

			// 入力チェック
			$error = $this->_checkInput($input);

			// 入力チェックでエラーがなければ認証を試みる
			if (empty($error) && Util::getInputStatus($this->getRequest())) {
				// 認証に成功
				if (Auth::login($db, $input['user_id'], $input['password'])) {
					// 確認画面へ遷移
					$this->redirect("/index/confirm/");
				}
				// 認証に失敗
				else {
					$error['auth'] = self::ERR_USER_EQUAL;
				}
			}

			// ユーザー
			$user_dao = new UserDao($db);
			$user_dao->find("vagrant");
			$user_daob = new UserDao($db);
			$user_daob->find("test");
			$user_dao->union($user_daob);

			// Viewへの値を渡す
			$this->view->input = $input;
			$this->view->error = $error;
			$this->view->user = $user_dao->fetchAll();

			// コミット
			$db->commit();
		}
		catch (Zend_Exception $e) {
			// ロールバック
			$db->rollback();

			// エラーの処理

			// レンダリング(ビューの自動表示)をOFF
			$this->_helper->viewRenderer->setNoRender();
			$this->getResponse()->setHeader('Content-Type', 'text/plain');

			echo "error\n\n";
			echo $e->getMessage();
		}
	}

	/**
	 * 確認画面
	 */
	public function confirmAction() {
		try {
			// 認証
			$auth = Util::auth();

			// Viewへの値を渡す
			$this->view->userId = $auth->getIdentity();
		}
		catch (Zend_Exception $e) {
			// エラーの処理

			// レンダリング(ビューの自動表示)をOFF
			$this->_helper->viewRenderer->setNoRender();
			$this->getResponse()->setHeader('Content-Type', 'text/plain');

			echo "error\n\n";
			echo $e->getMessage();
		}
	}

	/**
	 * 入力チェック
	 *
	 * @param array $input 入力リクエスト
	 * @return array 入力エラー内容
	 */
	private function _checkInput($input) {
		// エラー変数の初期化
		$error = array(); // Util::cloneArrayKey($input);

		// 未入力の場合は何もしない
		if (!Util::getInputStatus($this->getRequest())) {
			return $error;
		}

		// 入力チェック
		$error = Check::validate($input, array(
			// ユーザーID
			'user_id' => array(
				// 空のチェック
				array("empty", self::ERR_USERID_EMPTY)
			),
			// パスワード
			'password' => array(
				// 空のチェック
				array("empty", self::ERR_PASSWORD_EMPTY)
			)
		));

		// エラー内容を返す
		return $error;
	}
}