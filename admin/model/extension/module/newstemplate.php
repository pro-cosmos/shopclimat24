<?php
class ModelExtensionModuleNewstemplate extends Model {
	
	public function createNewstemplate()
	{
			
		$res0 = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."newstemplate'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `".DB_PREFIX."newstemplate` (
				  `code` varchar(255) NOT NULL,
				  `news_id` int(11) NOT NULL AUTO_INCREMENT,
				  `news_h1` longtext NOT NULL,
				  `news_image` longtext NOT NULL,
				  `news_date` varchar(255) NOT NULL,
				  `news_on` varchar(255) NOT NULL,
				  `news_sort` varchar(255) NOT NULL,
				  `news_text` longtext NOT NULL,
				  `serialized` int(11) NOT NULL,
				  PRIMARY KEY (`news_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function getNewstemplate() {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."newstemplate");

		return $query->rows;
	}
	
	public function editNewstemplate($code, $data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "newstemplate` WHERE `code` = '" . $this->db->escape($code) . "'");
		
		if (isset($data['featured_news'])) {
			foreach ($data['featured_news'] as $key) {
				if (!isset($key['news_on'])) {$news_on = "news_on = '0'";} else {$news_on = "news_on = '" . $this->db->escape($key['news_on']) . "'";}
				$this->db->query("INSERT INTO " . DB_PREFIX . "newstemplate SET `code` = '" . $this->db->escape($code) . "', news_h1 = '" . $this->db->escape(json_encode($key['news_h1'])) . "', news_image = '" . $this->db->escape($key['news_image']) . "', news_date = '" . $this->db->escape($key['news_date']) . "'," . $news_on . ", news_sort = '" . $this->db->escape($key['news_sort']) . "', news_text = '" .  $this->db->escape(json_encode($key['news_text'])) . "', serialized = '1'");
			}
		}
	}
	
}