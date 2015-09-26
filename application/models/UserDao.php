<?php
// include
require_once APP_COMMON . 'Table.php';

/**
 * ユーザーテーブルDAO
 *
 * @author Navi
 * @version 1.0.0
 */
class UserDao extends Base_Db_Table
{
	/** テーブル名 */
	protected $_name = "user";

	/**
	 * 値を取得する
	 */
	public function find($where) {
		// select
		$select = $this->select();

		// where
		$select->where("user_id = ?", $where);

		// 結果を返す
		return $this;
	}
}
