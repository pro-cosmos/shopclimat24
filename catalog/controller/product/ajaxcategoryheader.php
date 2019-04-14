<?php
class ControllerProductAjaxcategoryheader extends Controller {
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');
		
		$this->load->model('catalog/ajaxcategoryheader');

		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();
				$data_manufacturers = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					
					// Level 3
					$children_3_data = array();

					$children_3 = $this->model_catalog_category->getCategories($child['category_id']);

					foreach ($children_3 as $child_3) {
						$filter_data_3 = array(
							'filter_category_id'  => $child_3['category_id'],
							'filter_sub_category' => true
						);

						$children_3_data[] = array(
							'name'  => $child_3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data_3) . ')' : ''),
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child_3['category_id'])
						);
					}

					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);
					$children_data[] = array(
						'name'  		=> $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  		=> $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'children' 		=> $children_3_data
					);
				}
				if ($category['image']) {
					$image = $this->model_tool_image->resize($category['image'], 70, 70);
				} else {
					$image = false;
				}
				
				$category_manufacturer = $this->model_catalog_ajaxcategoryheader->getCategoryManufacturers($category['category_id']);
				if ($category_manufacturer) {
					foreach ($category_manufacturer as $manuf) {
						$data_manufacturers[] = array(
							'name'  		=> $manuf['name'],
							'image'  		=> $this->model_tool_image->resize($manuf['image'], 50, 50),
							'href'  		=> $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manuf['manufacturer_id'])
						);
					}
				}
					
				// Level 1
				$data['categories'][] = array(
					'category_id'     	=> $category['category_id'],
					'name'     			=> $category['name'],
					'children' 			=> $children_data,
					'manufacturers' 	=> $data_manufacturers,
					'image'    			=> $image,
					'description'  		=> utf8_substr(strip_tags(html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8')), 0, 500),
					'column'   			=> $category['column'] ? $category['column'] : 1,
					'href'     			=> $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		return $this->load->view('product/ajaxcategoryheader', $data);
	}
}
