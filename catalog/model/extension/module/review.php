<?php
class ModelExtensionModuleReview extends Model {

	public function getReviews() {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."review WHERE status = 1"); 
		return $query->rows;
		
	}

}