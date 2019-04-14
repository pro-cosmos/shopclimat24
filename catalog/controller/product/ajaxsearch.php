<?php
class ControllerProductAjaxsearch extends Controller {
	public function ajax() {
		$data = array();
		if (isset($this->request->get['keyword'])) {
			$this->load->model('tool/image');
			$keywords = $this->request->get['keyword'];
			if( strlen($keywords) >= 1 ) {
				$add = '';
					$add .= ' OR (LOWER(pd.name) LIKE "%' . $this->db->escape($keywords) . '%"';
					$add .= ' OR LOWER(p.model) LIKE "%' . $this->db->escape($keywords) . '%"';
					$add .= ' OR LOWER(p.sku) LIKE "%' . $this->db->escape($keywords) . '%")';
				
				$add = substr($add, 4);
				$sql  = 'SELECT pd.product_id, pd.name, p.image, p.sku FROM ' . DB_PREFIX . 'product_description AS pd ';
				$sql .= 'LEFT JOIN ' . DB_PREFIX . 'product AS p ON p.product_id = pd.product_id ';
				$sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_store AS p2s ON p2s.product_id = pd.product_id ';
				if (isset($this->request->get['category_id'])) {$sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_category AS p3s ON p3s.product_id = pd.product_id ';}
				$sql .= 'WHERE ' . $add . ' AND p.status = 1 ';
				$sql .= 'AND pd.language_id = ' . (int)$this->config->get('config_language_id');
				$sql .= ' AND p2s.store_id =  ' . (int)$this->config->get('config_store_id');
				if (isset($this->request->get['category_id']) ) {$sql .= ' AND p3s.category_id = ' . $this->request->get['category_id'];}
				$sql .= ' ORDER BY p.sort_order ASC, LOWER(pd.name) ASC, LOWER(p.image) ASC';
				$sql .= ' LIMIT 15';
				$res = $this->db->query( $sql );
				if( $res ) {
					$data = ( isset($res->rows) ) ? $res->rows : $res->row;
					$basehref = 'product/product&product_id=';
					foreach( $data as $key => $values ) {
						if ($values['image']) {
							$image = $this->model_tool_image->resize($values['image'], 40, 40);
						} else {
							$image = false;
						}
						$data[$key] = array(
							'thumb' 		=> $image,
							'product_id' 	=> $values['product_id'],
							'sku' 			=> $values['sku'],
							'name'  		=> htmlspecialchars_decode($values['name'], ENT_QUOTES),
							'href'  		=> str_replace('&amp;', '&', $this->url->link($basehref . $values['product_id']))
						);
					}
				}
			}
		} echo json_encode( $data );
	}
}
