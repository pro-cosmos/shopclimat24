<?php
class ModelCatalogAjaxcategoryheader extends Model {
	public function getCategoryManufacturers($category_id) {
		$query = $this->db->query("SELECT DISTINCT m.manufacturer_id,m.name,m.image FROM " . DB_PREFIX . "manufacturer m INNER JOIN " . DB_PREFIX . "product p ON (m.manufacturer_id = p.manufacturer_id) INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p2s.product_id = p2c.product_id) INNER JOIN " . DB_PREFIX . "category_to_store c2s ON (p2c.category_id = c2s.category_id) INNER JOIN " . DB_PREFIX . "category_path cp ON (c2s.category_id = cp.category_id) WHERE cp.category_id IN (SELECT category_id FROM " . DB_PREFIX . "category_path WHERE path_id = '" . (int)$category_id . "')");

		return $query->rows;
	}
}