<?php

class AddTransactionPage extends Page {

	static $allowed_children = 'none';

	public static $db = array();
}

class AddTransactionPage_Controller extends Page_Controller {
	public static $allowed_actions = array(
			'AddForm',
			'AssignForm',
			'Categories'
	);

	public function init() {
		parent::init();
		Requirements::css(PRIVATE_ECONOMY_DIR . '/css/AddTransactionPage.css');
		Requirements::themedCSS('AddTransactionPage');
	}

	public function index() {
		return array(
				'Form' => $this->AddForm()
		);
	}

	public function AddForm() {
		$fields = new FieldSet(
			new TextareaField('Transactions', _t('AddTransactionPage', 'Transactions')));
		$types = ClassInfo::subclassesFor('Transaction');
		unset($types[0]);
		if (count($types) == 1)
			$fields->push(new HiddenField('Type', '', reset($types)));
		else
			$fields->push(new DropdownField('Type', _t('AddTransactionPage.Type', 'Type'), $types));
		$actions = new FieldSet(
			new FormAction('doSubmit', _t('AddTransactionPage.SubmitButton', 'Submit')));
		return new Form($this, 'AddForm', $fields, $actions);
	}

	public function doSubmit($data, Form $form) {
		if (!ClassInfo::exists($data['Type'])) {
			$form->setMessage(_t('AddTransactionPage.BadSubmit', 'Bad submit'), 'bad');
		} else {
			$class = singleton($data['Type']);
			$transactions = Transaction::parse($data['Type'], $data['Transactions'], true);
			//			if (!$transactions->exists())
			//				$form
			//					->setMessage(_t('AddTransactionPage.NoTransactions', 'No transactions'),
			//						'warning');
			//			else
			$form = $this->AssignForm();
		}

		return array(
				'Form' => $form
		);
	}

	public function AssignForm() {
		Requirements::javascript(PRIVATE_ECONOMY_DIR . '/js/float_header.js');
		Requirements::customScript($this->javascript());
		$fields = new FieldSet(
			$table = new TableField('Transactions', 'Transaction',
				array(
						'RegisterDate' => _t('Transaction.db_RegisterDate', 'Register Date'),
						'TransactionDate' => _t('Transaction.db_TransactionDate',
							'Transaction Date'),
						'Amount' => _t('Transaction.db_Amount', 'Amount'),
						'Comment' => _t('Transaction.db_Comment', 'Comment'),
						'CategoryTitle' => _t('Transaction.has_one_Category', 'Category'),
						'Private' => _t('Transaction.db_Private', 'Private'),
						'Duplicate' => _t('Transaction.db_Duplicate', 'Duplicate')
				),
				array(
						'RegisterDate' => 'TextField',
						'TransactionDate' => 'TextField',
						'Amount' => 'TextField',
						'Comment' => 'TextField',
						'CategoryTitle' => 'TextField',
						'Private' => 'CheckboxField',
						'Duplicate' => 'CheckboxField'
				), null, '`Unparsed` = 1 AND `OwnerID` = ' . Member::currentUserID(), true,
				'`TransactionDate` DESC LIMIT 30'));
		$actions = new FieldSet(
			new FormAction('doSave', _t('AddTransactionPage.SaveButton', 'Save')));
		return new Form($this, 'AssignForm', $fields, $actions);
	}

	public function doSave($data, Form $form) {
		if (!isset($data['Transactions']))
			return array(
					'Form' => $this->AssignForm()
			);
		$errors = array();
		$new_transactions = null;
		if (array_key_exists('new', $data['Transactions'])) {
			$new_transactions = $data['Transactions']['new'];
			unset($data['Transactions']['new']);
		}
		foreach ($data['Transactions'] as $id => $values) {
			if (isset($values['Duplicate'])) {
				DataObject::delete_by_id('Transaction', (int) $id);
				continue;
			}
			$transaction = DataObject::get_by_id('Transaction', (int) $id);
			if ($transaction) {
				$valid_transaction = true;
				foreach ($values as $field => $value) {
					if ($field == 'CategoryTitle') {
						$transaction->CategoryID = TransactionCategory::findCategoryID($value, true);
					} elseif ($transaction->hasField($field)) {
						$transaction->setCastedField($field, $value);
					} else {
						$valid_transaction = false;
						$errors[] = _t('AddTransactionPage.BadField',
							sprintf('Transactions don\'t have a field named %s', $field));
					}
				}
				if ($valid_transaction) {
					$transaction->Unparsed = false;
					$transaction->write();
				}
			} else {
				$errors[] = _t('AddTransactionPage.BadID',
					sprintf('No transaction found with ID %s', $id));
			}
		}

		if ($new_transactions && isset($new_transactions['Amount'])
				&& isset($new_transactions['Comment'])) {
			foreach ($new_transactions['Amount'] as $i => $amount) {
				if (!is_numeric(trim($amount)) || !isset($new_transactions['Comment'][$i])
						|| trim($new_transactions['Comment'][$i]) == '')
					continue;

				$amount = doubleval($amount);
				$comment = trim($new_transactions['Comment'][$i]);
				if (isset($new_transactions['RegisterDate'][$i])
						&& $time = strtotime($new_transactions['RegisterDate'][$i]))
					$registerDate = date('Y-m-d', $time);
				else
					$registerDate = date('Y-m-d');
				if (isset($new_transactions['TransactionDate'][$i])
						&& $time = strtotime($new_transactions['TransactionDate'][$i]))
					$transactionDate = date('Y-m-d', $time);
				else
					$transactionDate = date('Y-m-d');

				$transaction = new Transaction();
				$transaction->RegisterDate = $registerDate;
				$transaction->TransactionDate = $transactionDate;
				$transaction->Amount = $amount;
				$transaction->Comment = $comment;
				$transaction->Unparsed = false;
				if (isset($new_transactions['CategoryTitle'][$i]))
					$transaction->CategoryID = TransactionCategory::findCategoryID($new_transactions['CategoryTitle'][$i],
						true);
				if (isset($new_transactions['Private'][$i]))
					$transaction->Private = true;
				$transaction->write();
			}
		}

		if (empty($errors)) {
			if (DataObject::get_one('Transaction',
				sprintf('`OwnerID` = %s AND `Unparsed` = 1', Member::currentUserID())))
				$form = $this->AssignForm();
			else
				return Director::redirect($this->Link());
		} else {
			$form->setMessage(implode('<br>', $errors), 'bad');
		}

		return array(
				'Form' => $form
		);
	}

	public function Categories(SS_HTTPRequest $request) {
		$categories = DataObject::get('TransactionCategory',
			sprintf("`Title` LIKE '%%%s%%'", Convert::raw2sql($request->getVar('term'))));
		if ($categories)
			return '["' . implode('","', $categories->map('ID', 'Title')) . '"]';
		return '[]';
	}

	protected function javascript() {
		return <<<JS
			(function ($) {
				$(document).ready(function () {
					$(".TableField table.data input[id$='CategoryTitle']").on("click", function () {
						if (!$(this).hasClass("ui-autocomplete-input"))
							$(this).autocomplete({
								minLength: 2,
								source: "/AddTransactionPage_Controller/Categories"
							});
					});
				});
			}(jQuery));
JS;
	}
}
