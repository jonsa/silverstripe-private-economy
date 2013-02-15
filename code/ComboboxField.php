<?php

class ComboboxField extends DropdownField {

	public function Field() {
		Requirements::javascript(SAPPHIRE_DIR
						. '/thirdparty/jqueryui/minified/jquery.ui.autocomplete.js');
		$id = $this->id();
		Requirements::customScript("(function(\$) {\$(\"#$id\").combobox();}(jQuery));",
				"ComboboxField-$id");
		return parent::Field();
	}
}
