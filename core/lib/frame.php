<?php
class baseFrame {
	/* -- グローバル変数 -- */
	public $theme = "";
	public $page = "";
	public $layout = "default";
	public $control = "";
	public $action = "";
	public $view = "";
	public $param = array();

	/**
	 * コンストラクタ
	 */
	public function __construct($control, $param) {
		// コントローラー
		$this->control = $control;

		// 引数
		$this->param = $param;

		// アクション
		if (isset($param[0])) {
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
			$this->view = join("/", $param);
		}

		// 開始
		$this->AppStart();

		if ($isAction) {
			$this->{$this->action}();
		}
	}

	/**
	 * 開始
	 */
	public function AppStart() {
	}
}
