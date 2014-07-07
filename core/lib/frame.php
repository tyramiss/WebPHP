<?php
class baseFrame {
	/* -- グローバル変数 -- */
	public $theme;
	public $page;
	public $layout;
	public $control;
	public $action;
	public $view;
	public $param;

	/**
	 * コンストラクタ
	 */
	public function __construct($control, $param) {
		// デフォルトの設定
		$this->theme = "";
		$this->page = "";
		$this->layout = "default";

		// コントローラー
		$this->control = $control;

		// 引数
		$this->param = $param;

		// アクション
		if (isset($this->param[0])) {
			$this->action = array_shift($this->param);
		}
		if (!isset($this->action{0})) {
			$this->action = "index";
		}
		// 描画先はアクションと同じがデフォルト
		$this->view = $this->action;

		// アクションの有無
		$isAction = method_exists($this, $this->action);

		// アクションが存在していない場合は
		// パラメーター全てを繋げた先が描画先
		if (!$isAction) {
			if (isset($this->param[0])) {
				$this->view .= join("/", $this->param);
			}
			// ディレクトリの場合はindexを参照
			if (Dir::isRead($this->getViewPath(""))) {
				$this->view .= "/index";
			}
		}

		// 開始
		$this->AppStart();

		// アクション
		if ($isAction) {
			$this->{$this->action}();
		}

		// 表示
		$this->AppView();
	}

	/**
	 * 開始
	 */
	public function AppStart() {
	}

	/**
	 * 表示
	 */
	public function AppView() {
		// 404 : レイアウトファイルが存在しない
		if (!File::isRead($this->getLayoutPath())) {
			throw new NotFoundException($this->_errorOutPut("レイアウトファイルが存在しません。"));
		}
		// 404 : 表示するファイルが存在しない
		if (!File::isRead($this->getViewPath())) {
			throw new NotFoundException($this->_errorOutPut("表示ファイルが存在しません。"));
		}

		// テンプレートの読み込み
		ob_start();
		include $this->getViewPath();
		$content = ob_get_clean();

		// レイアウトに興して表示
		include $this->getLayoutPath();
	}

	/**
	 * レイアウトファイルのパスを返す
	 *
	 * @return string レイアウトファイルのパス。
	 */
	public function getLayoutPath($extension = CTP_EXTENSION) {
		return(VIEW_DIR . (isset($this->theme{0}) ? "/{$this->theme}/" : "/") . "layout/{$this->layout}{$extension}");
	}

	/**
	 * 表示ファイルのパスを返す
	 *
	 * @return string 表示ファイルのパス。
	 */
	public function getViewPath($extension = CTP_EXTENSION) {
		return(VIEW_DIR . (isset($this->theme{0}) ? "/{$this->theme}/" : "/") . "{$this->control}/{$this->view}{$extension}");
	}

	/**
	 * コントローラーファイルが読み込めるか
	 *
	 * @param string $name コントローラーの名前
	 * @return boolean コントローラーファイルが読み込めばTRUEを返す。
	 */
	public static function isControl($name) {
		return(File::isRead(CONTROLL_DIR . "/" . $name . PHP_EXTENSION));
	}

	/**
	 * コントローラーファイルのパスを返す
	 *
	 * @param string $name コントローラーの名前
	 * @return string コントローラーファイルのパス。
	 */
	public static function getControlPath($name) {
		return(CONTROLL_DIR . "/" . $name . PHP_EXTENSION);
	}

	/**
	 * エラーの出力
	 *
	 * @param string $message エラー内容
	 * @return string エラー出力内容
	 */
	private function _errorOutPut($message) {
		$params = join("/", $this->param);
		$controlFile = __FILE__;
		ob_start();
		echo <<< EOF
theme => "{$this->theme}"
page => "{$this->page}"
layout => "{$this->layout}"
control => "{$this->control}"
action => "{$this->action}"
view => "{$this->view}"
param => "{$params}"

control path => "{$controlFile}"
layout path => "{$this->getLayoutPath()}"
view path => "{$this->getViewPath()}"

{$message}
EOF;
		return(ob_get_clean());
	}
}
