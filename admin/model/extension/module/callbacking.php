<?php
class ModelExtensionModuleCallbacking extends Model {
	
	public function createCallbacking()
	{
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."callbacking'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "callbacking` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `date` varchar(255) NOT NULL,
				  `product_id` varchar(255) NOT NULL,
				  `option` varchar(255) NOT NULL,
				  `price` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `phone` varchar(255) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `qyantity` int(11) NOT NULL,
				  `href` longtext NOT NULL,
				  `comment` longtext NOT NULL,
				  `status` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function getCallbacking() {
		
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."callbacking'");
		if($res0->num_rows != 0){
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."callbacking"); 

		return $query->rows;
		}
	}
	
	public function deletecallbacking($id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "callbacking` WHERE `id` = '" . (int)$id . "'");
		
	}
	
	public function updatestatuscallbacking($id, $status) {
		$this->db->query("UPDATE " . DB_PREFIX . "callbacking SET status = '" . (int)$status . "' WHERE id = '" . (int)$id . "'");
	}
	
}