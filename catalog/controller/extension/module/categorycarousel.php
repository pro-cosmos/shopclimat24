<?php  
class ControllerExtensionModuleCategorycarousel extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/categorycarousel');
		
    	$data['heading_title'] = $this->language->get('heading_title');
		$data['heading_manuf'] = $this->language->get('heading_manuf');
		
		$data['kol_vo_podcateg'] = $this->language->get('kol_vo_podcateg');
		
		$this->load->model('catalog/category');
		
		$this->load->model('tool/image');
		
		$data['module'] = $setting;
		
		$this->document->addStyle('catalog/view/theme/unitystore/stylesheet/categorycarousel.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
        
        if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		
		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}
		
		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}
		
		$data['gen1'] = rand(500, 900); $data['gen2'] = rand(5000, 10000); $data['gen3'] = rand(11000, 15000);
							
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories_m'] = array();
		
		if (!isset($setting['width'])) {$setting['width'] = 50;}
		if (!isset($setting['height'])) {$setting['height'] = 50;}
		
		$data['no_thumb'] = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);

		$categories_m = $this->model_catalog_category->getCategories(0);
		
		if (isset($setting['style_on_off_categ_2_module_carousel'])) {$data['style_on_off_categ_2_module_carousel'] = $setting['style_on_off_categ_2_module_carousel'];}
		if (isset($setting['style_kol_vo_podcateg'])) {$data['style_kol_vo_podcateg'] = $setting['style_kol_vo_podcateg'];}

		foreach ($categories_m as $category_m) {

			$children_data = array();

			$children_m = $this->model_catalog_category->getCategories($category_m['category_id']);
			
			$kol = 0;
			
			foreach ($children_m as $child_m) {
			
				$kol = $kol + 1;

				$children_data[] = array(
					'category_id' => $child_m['category_id'],
					'name'        => $child_m['name'],
					'href'        => $this->url->link('product/category', 'path=' . $category_m['category_id'] . '_' . $child_m['category_id']),
					'image'       => $this->model_tool_image->resize($category_m['image'], 50, 50),
					'kol'         => $kol
				);		
			}

			$data['categories_m'][] = array(
				'category_id' => $category_m['category_id'],
				'name'        => $category_m['name'],
				'children'    => $children_data,
				'href'        => $this->url->link('product/category', 'path=' . $category_m['category_id']),
				'thumb'       => $this->model_tool_image->resize($category_m['image'], $setting['width'], $setting['height'])
			);	
		}
		
		$this->load->model('catalog/manufacturer');
		
		$data['manufacturers'] = array();
		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		
		foreach ($manufacturers as $manufacturer) {
			$data['manufacturers'][] = array(
				'manufacturer_id' => $manufacturer['manufacturer_id'],
				'name'        	  => $manufacturer['name'],
				'image'           => $this->model_tool_image->resize($manufacturer['image'], 80, 80),
				'link' 		      => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
			);
		}

		return $this->load->view('extension/module/categorycarousel', $data);
  	}
}
?>