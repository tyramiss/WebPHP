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
		try {
			// すでにログインしている場合はログアウト
			$auth = Zend_Auth::getInstance();
			if ($auth->hasIdentity()) {
				$auth->clearIdentity();
			}

			// POSTから入力データを取得
			$input = Util::getPost($this->_request, array(
				'user_id'  => "",
				'password' => ""
			));

			// 入力チェック
			$error = $this->_checkInput($input);

			// 入力チェックでエラーがなければ認証を試みる
			if (empty($error)) {
				// 認証はユーザー情報を使用する
				$user_dao = new UserDao();
				$db = new Zend_Auth_Adapter_DbTable(
					$user_dao->db,
					$user_dao->name,
					$user_dao->id,
					$user_dao->password,
					$user_dao->treatment
				);

				// 入力されたユーザーID
				$db->setIdentity($input['user_id']);
				// 入力されたパスワード
				$db->setCredential($input['password']);

				// 認証に成功
				if ($auth->authenticate($db)->isValid()) {
					// 確認画面へ遷移
					$this->redirect("/index/confirm/");
				}
				// 認証に失敗
				else {
					$error['auth'] = self::ERR_USER_EQUAL;
				}
			}

			// Viewへの値を渡す
			$this->view->input = $input;
			$this->view->error = $error;
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
		if (!Util::getInputStatus($this->_request)) {
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