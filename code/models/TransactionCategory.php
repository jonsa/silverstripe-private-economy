<?php

class TransactionCategory extends DataObject {
	public static $db = array(
			'Title' => 'Varchar(100)'
	);

	public static $has_many = array(
			'Transactions' => 'Transaction'
	);

	public static function findCategory($title, $createIfNotFound = false) {
		if (empty($title))
			return null;

		// DataObject will manage caching
		$category = DataObject::get_one('TransactionCategory',
			sprintf("`Title` = '%s'", Convert::raw2sql($title)));

		if (!$category) {
			$category = new TransactionCategory();
			$category->Title = $title;
			$category->write();
		}

		return $category;
	}

	public static function findCategoryID($title, $createIfNotFound = false) {
		$category = TransactionCategory::findCategory($title, $createIfNotFound);
		if ($category)
			return $category->ID;
		return 0;
	}
}
