<?php
class ModelExtensionModuleNewstemplate extends Model {
	
	public function getNewstemplate() {
		
		$res0 = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."newstemplate'");
		if($res0->num_rows != 0){
			
			$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."newstemplate");

			return $query->rows;
			
		}
	}
	
}