<?php
class ModelExtensionModuleSettemplate extends Model {
	
	public function createSetting() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."settemplate'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "settemplate` (
				  `code` varchar(255) NOT NULL,
				  `key` varchar(255) NOT NULL,
				  `value` longtext NOT NULL,
				  `serialized` int(11) NOT NULL
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
	
	}
	
	public function getSetting($code) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "settemplate WHERE `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = json_decode($result['value'], true);
			}
		}

		return $setting_data;
	}

	public function editSetting($code, $data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "settemplate` WHERE `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "settemplate SET `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "settemplate SET `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1'");
				}
			}
		}
	}

	public function deleteSetting($code) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "settemplate WHERE `code` = '" . $this->db->escape($code) . "'");
	}

}
