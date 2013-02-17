<?php

class Transaction extends DataObject {
	protected static $category_cache = null;

	protected $duplicate = false;

	public static $db = array(
			'RegisterDate' => 'Date',
			'TransactionDate' => 'Date',
			'Amount' => 'Currency',
			'Comment' => 'Varchar',
			'Private' => 'Boolean',
			'ParsedFrom' => 'Text',
			'Duplicate' => 'Boolean',
			'Unparsed' => 'Boolean'
	);

	public static $has_one = array(
			'Owner' => 'Member',
			'Category' => 'TransactionCategory'
	);

	public static $defaults = array(
			'Unparsed' => '1'
	);

	public static $default_sort = 'TransactionDate DESC';

	public function onBeforeWrite() {
		if (!$this->ID)
			$this->OwnerID = Member::currentUserID();
		else
			$this->Unparsed = false;
		parent::onBeforeWrite();
	}

	public function MarkAsDuplicate() {
		$this->duplicate = true;
	}

	public function IsDuplicate() {
		return $this->duplicate;
	}

	public function getCategoryTitle() {
		if ($this->CategoryID)
			return $this->Category()->Title;
		return '';
	}

	public static function parse($class, $text, $writeToDB = false) {
		$children = ClassInfo::subclassesFor('Transaction');
		if (!array_key_exists($class, $children))
			trigger_error("Method called with invalid class $class", E_USER_WARNING);
		$transactions = singleton($class)->parseText($text);
		$existing = DataObject::get('Transaction', '`OwnerID` = ' . Member::currentUserID());
		foreach ($transactions as $transaction) {
			$transaction->findSuitableCategory();
			if ($existing && $existing->find('ParsedFrom', $transaction->ParsedFrom))
				$transaction->Duplicate = true;
			if ($writeToDB)
				$transaction->write();
		}
		return $transactions;
	}

	protected function findSuitableCategory() {
		$comment = $this->getField('Comment');
		if (empty($comment))
			return;

		if (is_null(self::$category_cache)) {
			self::$category_cache = false;
			$sql = new SQLQuery();
			$sql->select('`Comment`, `CategoryID`, COUNT(`ID`) AS `Count`')->from('Transaction')
				->where('`Comment` IS NOT NULL AND `Comment` != \'\'')->groupby('`Comment`')
				->orderby('`Count` DESC');
			$result = $sql->execute();
			if ($result)
				self::$category_cache = $result;
		}

		if (!self::$category_cache)
			return;

		$best = null;
		foreach (self::$category_cache as $row) {
			similar_text($comment, $row['Comment'], &$row['score']);
			//$row['score'] = levenshtein($comment, $row['Comment']);
			// exact match
			if ($row['score'] == 100) {
				$best = $row;
				break;
			}

			if ($row['score'] > 60 && (!$best || $row['score'] > $best['score']))
				$best = $row;
		}

		if ($best)
			$this->CategoryID = $best['CategoryID'];
	}
}
