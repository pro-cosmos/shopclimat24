<?php
class ControllerExtensionModuleReviews extends Controller {
	public function index($setting) {
		$this->language->load('extension/module/reviews'); 
		$this->language->load('extension/module/langtemplates'); 
		
		$this->document->addStyle('catalog/view/theme/unitystore/stylesheet/carousel_reviews.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
        
        static $mod_revies = 0;

      	$data['heading_title'] = $this->language->get('heading_title');
		
		$data['button_cart'] = $this->language->get('button_cart');
		
		$data['podrobnee'] = $this->language->get('podrobnee');
		$data['heading_tit'] = $this->language->get('heading_tit');
		
		$data['setting'] = $setting;
		
		$this->load->model('catalog/product'); 
        
        $this->load->model('catalog/review');
		
		$this->load->model('tool/image');

		$data['products'] = array();

		$products = explode(',', $this->config->get('reviews_product'));		

		if (empty($setting['limit'])) {
			$setting['limit'] = 5;
		}
		
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['no_image'] = $this->model_tool_image->resize('no_image.jpg', $setting['width'], $setting['height']);
        
        $data['limit'] = $setting['limit'];
		
		$data['position'] = $setting['position'];
		$data['width'] = $setting['width'];
		$data['height'] = $setting['height'];
		
		$data['gen1'] = rand(100, 500);
		
		$this->load->model('extension/module/review');
		$reviews = $this->model_extension_module_review->getReviews();
		
		$prods = array();
		
		foreach ($reviews as $product_id) {
			$prods[] = array(
				'product_id'  	=> $product_id['product_id'],
				'date_added'    => $product_id['date_added'],
				'text'    => $product_id['text']
			);
		}
		
		

		if (isset($prods)) {
			$sort = array(); 
			foreach ($prods as $key => $value) {
				$sort[$key] = $value['date_added'];
			}
			array_multisort($sort, SORT_DESC, $prods);
		}
		

		$producs = array_slice($prods, 0, (int)$setting['limit']);
		
		foreach ($producs as $product_id) {
			
			$product_info = $this->model_catalog_product->getProduct($product_id['product_id']);
            $review_tots = $this->model_catalog_review->getTotalReviewsByProductId($product_id['product_id']);
			
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}
                
				$text_review = $product_id['text'];
				
				If (empty($text_review)) {
					$text_review = false;
				}
				
                $review_tot = (int)$review_tots;
				
				if ($review_tot > 0) {
					$add_reviews = $this->url->link('extension/module/carous/review', 'product_id=' . $product_info['product_id']);
				} else {
					$add_reviews = false;
				}
                
				$data['products'][] = array(
					'product_id' 	=> $product_info['product_id'],
					'thumb'   	 	=> $image,
					'name'    	 	=> $product_info['name'],
					'price'   	 	=> $price,
					'special' 	 	=> $special,
					'rating'     	=> $rating,
					'add_reviews'   => $add_reviews,
                    'text_review'   => $text_review,
                    'review_tot'    => $review_tot,
					'reviews'    	=> sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
					'href'    	 	=> $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
				);
			}
		}
        
        $data['mod_revies'] = $mod_revies++;

		if ($data['products']) {
			return $this->load->view('extension/module/reviews', $data);
		}
	}
}
?>