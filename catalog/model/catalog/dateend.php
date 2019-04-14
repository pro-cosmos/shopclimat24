<?php
class ModelCatalogDateend extends Model {

	public function getProductSpecialsDateEnd($product_id) {
		$query = $this->db->query("SELECT ps.date_end FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) AND ps.product_id = '" . (int)$product_id . "'");

		if (isset($query->row['date_end'])) {
			return $query->row['date_end'];
		} else {
			return false;
		}
	}
}
