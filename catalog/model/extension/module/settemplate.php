<?php
class ModelExtensionModuleSettemplate extends Model {
	
	public function getSetting($code) {
		$setting_data = array();
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."settemplate'");
		
		if($res0->num_rows != 0){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "settemplate WHERE `code` = '" . $this->db->escape($code) . "'");

			foreach ($query->rows as $result) {
				if (!$result['serialized']) {
					$setting_data[$result['key']] = $result['value'];
				} else {
					$setting_data[$result['key']] = json_decode($result['value'], true);
				}
			}
		}

		return $setting_data;
	}

}
