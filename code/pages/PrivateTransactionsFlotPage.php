<?php
/**
 * User: Jonas
 * Date: 2013-02-15
 * Time: 19:20
 */
class PrivateTransactionsFlotPage extends SharedTransactionsFlotPage {
	public $Align = "center";

	protected function getTransactions($filter = null) {
		$where = '`Private` = 1 AND `Unparsed` = 0 AND `OwnerID` = ' . Member::currentUserID();
		if (is_string($filter) && strlen($filter))
			$where .= ' AND ' . $filter;
		return DataObject::get('Transaction', $where);
	}
}

class PrivateTransactionsFlotPage_Controller extends SharedTransactionsFlotPage_Controller {
}
