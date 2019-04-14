<?php
class ControllerExtensionModuleManuf extends Controller {
	public function index($setting) {
		$this->language->load('extension/module/manuf'); 

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		
		$data['text_manufacturers'] = $this->language->get('text_manufacturers');
		$data['all'] = $this->language->get('all');
		$data['all_href'] = $this->url->link('product/manufacturer');
		
		$this->load->model('catalog/manufacturer');
		
		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		
		$data['manufacturers'] = array();
		if ($manufacturers) {
			foreach ($manufacturers as $manufacturer) {
				$data['manufacturers'][] = array(
					'name' => $manufacturer['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
				);
			}
		}
		
		$data['url'] = $this->url;

		return $this->load->view('extension/module/manuf', $data);

		$this->render();
	}
}
?>