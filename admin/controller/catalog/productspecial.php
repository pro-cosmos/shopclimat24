<?php
class ControllerCatalogProductspecial extends Controller {

	public function autocompletespecial() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$special = $this->model_catalog_product->getProductSpecials($result['product_id']);
				
				if ($special) {
					foreach ($special as $key => $value) {
						if ($result['product_id'] == $value['product_id']) {
							$json[] = array(
								'product_id' => $result['product_id'],
								'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
								'date_end'	 => $value['date_end'],
							);
						}
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
