<?php
class ControllerExtensionModuleAlogincategory extends Controller {
	public function index() {
		$this->load->language('extension/module/category');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->load->model('tool/image');
		$this->load->language('extension/module/langtemplates');
		$data['heading_categ'] = $this->language->get('heading_categ');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category1_id'] = $parts[0];
		} else {
			$data['category1_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['category2_id'] = $parts[1];
		} else {
			$data['category2_id'] = 0;
		}

		if (isset($parts[2])) {
			$data['category3_id'] = $parts[2];
		} else {
			$data['category3_id'] = 0;
		}

		if (isset($parts[3])) {
			$data['category4_id'] = $parts[3];
		} else {
			$data['category4_id'] = 0;
		}
		
		$data['categories_massiv1'] = array();

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$categories_1 = $this->model_catalog_category->getCategories(0);
						
		foreach ($categories_1 as $category_1) {
			
				$categories_massiv2 = array();
				$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
				
				foreach ($categories_2 as $category_2) {
					
					$categories_massiv3 = array();
					$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				
					foreach ($categories_3 as $category_3) {
					
						$categories_massiv4 = array();
						$categories_4 = $this->model_catalog_category->getCategories($category_3['category_id']);
						
						foreach ($categories_4 as $category_4) {
							
							$categories_massiv4[] = array(
							'category_id'	=> $category_4['category_id'],
							'name'			=> $category_4['name'],
							'href'			=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'] . '_' . $category_4['category_id'])
							);
							
						}
					
						$categories_massiv3[] = array(
							'category_id'	=> $category_3['category_id'],
							'name'			=> $category_3['name'],
							'href'			=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id']),
							'categories_4'	=> $categories_massiv4
						);
						
					}
				
					$categories_massiv2[] = array(
						'category_id'	=> $category_2['category_id'],
						'name'			=> $category_2['name'],
						'href'			=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id']),
						'image'			=> $this->model_tool_image->resize($category_2['image'], 50, 50),
						'categories_3'	=> $categories_massiv3
					);
				}
				
				$data['categories_massiv1'][] = array(
					'top'			=> $category_1['top'],
					'image'			=> $this->model_tool_image->resize($category_1['image'], 50, 50),
					'description' 	=> utf8_substr(strip_tags(html_entity_decode($category_1['description'], ENT_QUOTES, 'UTF-8')), 0, 60) . '..',
					'category_id'	=> $category_1['category_id'],
					'name'			=> $category_1['name'],
					'column'		=> $category_1['column'] ? $category_1['column'] : 1,
					'href'			=> $this->url->link('product/category', 'path=' . $category_1['category_id']),
					'categories_2'	=> $categories_massiv2
				);
		}

		return $this->load->view('extension/module/alogincategory', $data);
	}
}