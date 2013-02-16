<?php
/**
 * User: Jonas
 * Date: 2013-02-15
 * Time: 19:20
 */
class SharedTransactionsFlotPage extends Page {
	/**
	 * @var null|ArrayData
	 */
	private $total_bar = null;

	/**
	 * @var null|ArrayData
	 */
	private $total_point = null;

	/**
	 * @var null|DataObjectSet
	 */
	private $category_data = null;

	public function getTotalBarData() {
		if (is_null($this->total_bar))
			$this->createData();
		return $this->total_bar;
	}

	public function getTotalPointData() {
		if (is_null($this->total_point))
			$this->createData();
		return $this->total_point;
	}

	public function getCategoryData() {
		if (is_null($this->category_data))
			$this->createData();
		return $this->category_data;
	}

	protected function getTransactions() {
		return DataObject::get('Transaction', '`Private` = 0');
	}

	public function createData() {
		$data = array();
		foreach ($this->getTransactions() as $transaction) {
			if ($transaction->OwnerID == 0 || $transaction->CategoryID == 0)
				continue;
			$data[$transaction->CategoryID][$transaction->OwnerID][substr($transaction->TransactionDate, 0, 7)][] = $transaction;
		}

		$total_array = array();
		$total_labels = array();
		$data_array = array();
		foreach ($data as $categoryId => $users) {
			$category = DataObject::get_by_id('TransactionCategory', $categoryId);
			ksort($users);
			$json_data = array();
			$data_labels = array();
			foreach ($users as $userId => $labels) {
				$user = DataObject::get_by_id('Member', $userId);
				ksort($labels);
				$user_data = array(
					'label' => $user->getTitle(),
					'data' => array()
				);
				foreach ($labels as $label => $transactions) {
					$label = (int) strtotime($label . '-01 00:00:00');
					$data_labels[$label] = $label;
					$amount = 0;
					foreach ($transactions as $transaction)
						$amount += $transaction->Amount;
					$user_data['data'][$label] = array($label, -$amount);
					$total_array[$userId][$label] = (double) $total_array[$label][$userId] + $amount;
				}
				$json_data[] = $user_data;
			}
			$total_labels = array_merge($total_labels, $data_labels);
			foreach ($json_data as $i => $category_data) {
				$missing = array_diff_key($data_labels, $category_data['data']);
				foreach ($data_labels as $label) {
					if (in_array($label, $missing))
						$category_data['data'][$label] = array($label, 0);
					$category_data['data'][$label][0] *= 1000.0;
					$category_data['data'][$label][0] -= $i * count($category_data['data'][$label]) * 350000000;
				}
				ksort($category_data['data']);
				$json_data[$i]['data'] = array_values($category_data['data']);
			}
			$json = json_encode($json_data);
			if (!$json)
				$json = '[]';
			$data_array[] = new ArrayData(array(
				'Id' => $categoryId,
				'CategoryTitle' => $category->getTitle(),
				'Category' => $category,
				'Data' => $json,
				'Labels' => json_encode(array_values($data_labels))
			));
		}
		$this->category_data = new DataObjectSet($data_array);
		$this->category_data->sort('CategoryTitle');

		$total_labels = array_flip($total_labels);
		ksort($total_labels);
		$total_bar = array();
		$total_point = array();
		$i = 0;
		foreach ($total_array as $userId => $labels) {
			$user = DataObject::get_by_id('Member', $userId);
			$user_bar = array(
				'label' => $user->getTitle(),
				'data' => array()
			);
			$user_point = array(
				'label' => $user->getTitle(),
				'data' => array()
			);
			$amount = 0;
			foreach ($total_labels as $label => $junk) {
				$date_label = $label * 1000.0 - $i * count($total_labels) * 65000000;
				if (isset($labels[$label])) {
					$amount += -$labels[$label];
					$user_bar['data'][] = array($date_label, -$labels[$label]);
				} else {
					$user_bar['data'][] = array($date_label, 0);
				}
				$user_point['data'][] = array($label * 1000.0, $amount);
			}
			$total_bar[] = $user_bar;
			$total_point[] = $user_point;
			$i++;
		}
		$this->total_bar = new ArrayData(array(
			'Data' => json_encode($total_bar)
		));
		$this->total_point = new ArrayData(array(
			'Data' => json_encode($total_point)
		));
	}
}

class SharedTransactionsFlotPage_Controller extends Page_Controller {
	public function init() {
		parent::init();
		Requirements::set_write_js_to_body(false);
		Requirements::javascript(PRIVATE_ECONOMY_DIR . '/js/flot/jquery.flot.js');
		Requirements::javascript(PRIVATE_ECONOMY_DIR . '/js/SharedTransactionsFlotPage.js');
		Requirements::css(PRIVATE_ECONOMY_DIR . '/css/SharedTransactionsFlotPage.css');
	}
}
