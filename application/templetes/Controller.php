<?php
// include
require_once APP_MODEL . 'TempleteDao.php';		/* 連携するテーブルのDAOを書きます */

/**
 * コントローラーのテンプレート
 *
 * コントローラーの説明
 * こんなコントローラーであるとか、書きたいこといろいろと
 * 好きなことを書いてください
 *
 * @author Navi
 * @version 1.0.0
 */
class IndexController extends Zend_Controller_Action
{
	/** 定数 */
	const CONST_TAMPLETE = "TEMPLETE";		/* 定数は全て大文字を使用してください */

	/** グローバル変数 */
	public $pbTemplete;		/* メンバ変数名はキャメルケースを使用してください */

	/** プライベート変数 */
	private $_prTemplete;	/* プライベート変数/関数の先頭には_(アンダーバー)を付けるようにしてください */

	/**
	 * 初期化
	 */
	public function init() {
		/* メンバ変数を初期化する為の関数としてあります */
		$this->pbTemplete = "Templete";
		$this->_prTemplete = "Templete";
	}

	/**
	 * 共通アクション前関数
	 */
	public function preDispatch() {
		/* 各アクション実行前のに呼び出され共通で処理するところになります */
	}

	/**
	 * アクションの名前
	 */
	public function indexAction() {
		/*
		 * TryCatchで全体を包みエラーを掴めるようにしておいてください
		 * エラーを掴んだ時に以降の処理をスキップがされるようになっています
		 */
		try {
			// リクエスト処理

			// GETからIDを取得
			$user_id = $this->_request->getQuery("id", "templete");
			/* 第一引数がGETで渡された変数名、第二引数が存在しない時のデフォルト値になっています */

			/* ローカル変数はスネークケース */

			// POSTからパスワードを取得
			$password = $this->_request->getPost("password", "templete");
			/* 第一引数がPOSTで渡された変数名、第二引数が存在しない時のデフォルト値になっています */


			// DAOからの読み込み

			// Templeteから読み込み
			$templete_dao = new TempleteDao();	/* DAOクラスの変数名はクラス名を全て小文字にしスネークケースにしてください */
			$templete_list = $templete_dao->find(array('id=?' => $id, 'password=' => $password));


			// View関連

			// Viewへの値を渡す
			$this->view->templeteList = $templete_list;
			/* $this->view以下の変数はView上ではメンバ変数となるのでキャメルケースで書かれています */
		}
		catch (Zend_Exception $e) {
			// エラーの処理
		}
	}

	/**
	 * アクションの名前
	 */
	public function inputAction() {
		try {
			// リクエスト処理

			// POSTから入力データを取得
			$input = array(
				'id'       => $this->_request->getPost("id",       "templete"),
				'password' => $this->_request->getPost("password", "templete")
			);


			// DAOからの読み込み

			// Templeteから読み込み
			$templete_dao = new TempleteDao();	/* DAOクラスの変数名はクラス名を全て小文字にしスネークケースにしてください */
			$templete_data = $templete_dao->findRow(array('id=?' => $id, 'password=' => $password));


			// 入力チェック

			// エラー変数の初期化
			$error = array();
			foreach (array_keys($input) as $key) {
				// 初期化
				$error[$key] = null;
			}

			// ID
			if (empty($input['id'])) {
				$error['id'] = "IDが入力されていません";
			}
			else if ($input['id'] != $templete_data['id']) {
				$error['id'] = "IDが正しくありません";
			}

			// Password
			if (empty($input['password'])) {
				$error['password'] = "パスワードが入力されていません";
			}
			else if ($input['password'] != $templete_data['password']) {
				$error['password'] = "パスワードが正しくありません";
			}


			// View関連

			// Viewへの値を渡す
			$this->view->input = $input;
			$this->view->error = $error;
		}
		catch (Zend_Exception $e) {
			// エラーの処理
		}
	}
}