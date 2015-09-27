<?php
// include
require_once APP_COMMON . 'Controller.php';	// コントローラー基本クラス

// DAO
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
class templeteController extends Base_Controller_Action
{
	/** 定数 */
	const CONST_TAMPLETE = "TEMPLETE";		/* 定数は全て大文字を使用してください */

	/** 入力エラー内容 */
	const ERR_ID_EMPTY       = "IDが入力されていません";
	const ERR_ID_EQUAL       = "IDが正しくありません";
	const ERR_PASSWORD_EMPTY = "パスワードが入力されていません";
	const ERR_PASSWORD_EQUAL = "パスワードが正しくありません";
	const ERR_DATE_EMPTY     = "日付が入力されていません";
	const ERR_DATE_VALIDATE  = "日付が正しくありません";
	const ERR_TIME_EMPTY     = "時間が入力されていません";
	const ERR_TIME_VALIDATE  = "時間が正しくありません";
	/* 定数の先頭は用途ごとに同じ名前で統一してください */
	/* 半角空白で揃えてあげると見やすくなることもあります(逆にない場合が見やすいこともあります) */


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
			$user_id = $this->getRecest()->getQuery("id", "templete");
			/* 第一引数がGETで渡された変数名、第二引数が存在しない時のデフォルト値になっています */

			/* ローカル変数はスネークケース */

			// POSTからパスワードを取得
			$password = $this->getRecest()->getPost("password", "templete");
			/* 第一引数がPOSTで渡された変数名、第二引数が存在しない時のデフォルト値になっています */


			// DAOからの読み込み

			// Templeteから読み込み
			$templete_dao = new TempleteDao();	/* DAOクラスの変数名はクラス名を全て小文字にしスネークケースにしてください */
			$templete_list = $templete_dao->find();


			// View関連

			// Viewへの値を渡す
			$this->view->templeteList = $templete_list;
			/* $this->view以下の変数はView上ではメンバ変数となるのでキャメルケースで書かれています */
		}
		catch (Zend_Exception $e) {
			// エラーの処理

			// 表示するページを切り替える
			$this->_forward("error", "index");	/* errorコントロラーのindexアクションを呼ぶ例 */
		}

		/*
		 * Action のおおまかな作り方は、
		 *   最初に設定となるデータ類を読み込んで
		 *   真ん中あたりで処理をして
		 *   最後に View へ渡す処理
		 * と言う形に合わせた方がメンテナンスが楽になるかな？
		 */
	}

	/**
	 * 入力画面
	 */
	public function inputAction() {
		try {
			// リクエスト処理

			// POSTから入力データを取得
			$input = Util::getPost($this->_request, array(
				'id'       => "templete",
				'password' => "templete",
				'date'     => Zend_Date::now()->toString(FORMAT_DATE),
				'time'     => Zend_Date::now()->toString(FORMAT_TIME)
			));
			/* array('<form> で渡された name 名', "初期値") */


			// DAOからの読み込み

			// Templeteから読み込み
			$templete_dao = new TempleteDao();
			$templete_data = $templete_dao->findRow(array('id=?' => "id", 'password=?' => "password"));


			// 入力チェック

			// 入力チェックエラー変数の初期化
			$error = array();

			// 入力状態を確認
			if (Util::getInputStatus($this->_request)) {
				// 入力チェック
				$error = Check::validate($input, array(
					// ID
					'id' => array(
						// 空のチェック
						array("empty", self::ERR_ID_EMPTY),
						// データベースと比較
						array("equal", self::ERR_ID_EQUAL, $templete_data['id'])
					),
					// パスワード
					'password' => array(
						// 空のチェック
						array("empty", self::ERR_PASSWORD_EMPTY),
						// データベースと比較
						array("equal", self::ERR_PASSWORD_EQUAL, $templete_data['password'])
					),
					// 日付
					'date' => array(
						// 空のチェック
						array("empty", self::ERR_DATE_EMPTY),
						// 日付の妥当性
						array("date", self::ERR_DATE_VALIDATE, FORMAT_DATE)
					),
					// 時間
					'time' => array(
						// 空のチェック
						array("empty", self::ERR_TIME_EMPTY),
						// 時間の妥当性
						array("date", self::ERR_TIME_VALIDATE, FORMAT_TIME)
					),
				));

				/* 独自または複雑な入力チェックはここから下に記述 */

				// IDとパスワードのどちらかが違う
				if ($input['id'] != $templete_data['id'] ||
					$input['password'] != $templete_data['password']) {
					$error['auth'] = ERR_ID_EQUAL;
				}
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