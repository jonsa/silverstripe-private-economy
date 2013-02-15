<?php

class SwedbankTransaction extends Transaction {
	
	public static $db = array(
	);

	public function parseText($text) {
		$rows = explode("\n", $text);
		$data = new DataObjectSet();
		if (!is_array($rows))
			return $data;
		foreach ($rows as $row) {
			$cells = array_map('trim', (array) explode("\t", $row));
			if (count($cells) == 6 && preg_match('/^[0-9 ,-]+$/', $cells[4])) {
				$transaction = new SwedbankTransaction();
				$transaction->RegisterDate = $cells[0];
				$transaction->TransactionDate = $cells[1];
				$transaction->Amount = floatval(str_replace(array(
									',', ' '
								),
								array(
									'.', ''
								),
								$cells[4]));
				$transaction->Comment = $cells[2];
				$transaction->ParsedFrom = trim($row);
				$data->push($transaction);
			}
		}
		return $data;
	}
}
