<?php
/**
 * ビュー
 */
class baseView {
	/* -- グローバル変数 -- */
	public $theme;
	public $page;
	public $layout;
	public $control;
	public $action;
	public $view;
	public $param;
	public $variable;
	public $global;

	/**
	 * コンストラクタ
	 */
	public function __construct($theme, $page, $layout, $control, $action, $view, $param, $variable) {
		// 初期化
		$this->theme = $theme;
		$this->page = $page;
		$this->layout = $layout;
		$this->control = $control;
		$this->action = $action;
		$this->view = $view;
		$this->param = $param;
		$this->variable = $variable;
		$this->global = array();

		// 404 : レイアウトファイルが存在しない
		if (!File::isRead($this->getLayoutPath())) {
			throw new NotFoundException($this->_errorOutPut("レイアウトファイルが存在しません。"));
		}
		// 404 : 表示するファイルが存在しない
		if (!File::isRead($this->getViewPath())) {
			throw new NotFoundException($this->_errorOutPut("表示ファイルが存在しません。"));
		}

		// 展開
		extract($this->variable);

		// テンプレートの読み込み
		ob_start();
		include $this->getViewPath();
		$content = ob_get_clean();

		// レイアウトに興して表示
		include $this->getLayoutPath();
	}

	/**
	 * エレメントファイルの読み込み。
	 *
	 * @param string $path エレメントファイルのパス。
	 */
	public function element($path) {
		// ファイルが存在しない
		$elementPath = VIEW_DIR . "/" . VIEW_ELEMENT_DIR . "/" . $path . CTP_EXTENSION;
		if (File::isRead($elementPath)) {
			throw new NotFoundException($this->_errorOutPut("レイアウトファイルが存在しません。"));
		}

		// 展開
		extract($this->variable);

		// 読み込み
		include $elementPath;
	}

	/**
	 * レイアウトファイルのパスを返す。
	 *
	 * @return string レイアウトファイルのパス。
	 */
	public function getLayoutPath($extension = CTP_EXTENSION) {
		return(VIEW_DIR . "/" . VIEW_LAYOUT_DIR . "/" . (isset($this->theme{0}) ? $this->theme : "") . $this->layout . $extension);
	}

	/**
	 * 表示ファイルのパスを返す。
	 *
	 * @return string 表示ファイルのパス。
	 */
	public function getViewPath($extension = CTP_EXTENSION) {
		return(VIEW_DIR . "/{$this->page}/" . (isset($this->theme{0}) ? $this->theme : "") . $this->control . $extension);
	}
}