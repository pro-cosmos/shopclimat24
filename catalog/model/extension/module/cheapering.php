<?php
class ModelExtensionModuleCheapering extends Model {
	public function createCheapering() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."cheapering'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "cheapering` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `date` varchar(255) NOT NULL,
				  `product_id` varchar(255) NOT NULL,
				  `option` varchar(255) NOT NULL,
				  `price` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `phone` varchar(255) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `qyantity` int(11) NOT NULL,
				  `href` longtext NOT NULL,
				  `comment` longtext NOT NULL,
				  `status` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function createCheaperingliveprice() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."quickpayliveprice'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "quickpayliveprice` (
				  `id` int(11) NOT NULL,
				  `product_id` int(11) NOT NULL,
				  `option` varchar(255) NOT NULL,
				  `qyantity` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
			$this->db->query("INSERT INTO " . DB_PREFIX . "quickpayliveprice SET product_id = '', `option` = '', id = '1'");
		}
		
		
	}
	
	public function writesendquick($data) {
		
		$this->load->language('extension/module/langtemplates');
		
		if (isset($data['option'])) {$options = " `option` = '" . $this->db->escape(json_encode($data['option'])) . "'";} else {$options = " `option` = '" . 0 . "'";}
		if($this->db->query("INSERT INTO " . DB_PREFIX . "cheapering SET date = '" . $this->db->escape($data['date']) . "', product_id = '" . $this->db->escape($data['prod_id']) . "', qyantity = '" . $this->db->escape($data['qyantity']) . "'," . $options . ", price = '" . (int)$data['price'] . "', name = '" . $this->db->escape($data['name']) . "', phone = '" . $this->db->escape($data['phone']) . "', email = '" . $this->db->escape($data['email']) . "', href = '" . $this->db->escape($data['href']) . "', comment = '" . $this->db->escape($data['comment']) . "', status = '0'")) {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			
			
			$email = $this->config->get('config_email');
			
			
			$data['name_klient'] = $this->language->get('name_klient');
			$data['telefon_klient'] = $this->language->get('telefon_klient');
			$data['tovar_klient'] = $this->language->get('tovar_klient');
			$data['href_tovar_klient'] = $this->language->get('href_tovar_klient');
			$data['message_klient'] = $this->language->get('message_klient');
			$data['text_desired_price'] = $this->language->get('text_desired_price');
			$data['text_search_cheaper'] = $this->language->get('text_search_cheaper');
			$data['text_price_tovar'] = $this->language->get('text_price_tovar');
			
			$data['to'] = $this->config->get('config_email');
			
			if (isset($data['prod_id'])) {
				$product_id = (int)$data['prod_id'];
			} else {
				$product_id = 0;
			}
			
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}
			
			$price_tovar = 0;
			
			if (!$data['special']) {$price_tovar = $data['price'];} else {$price_tovar = $data['special'];}
			
			$mess = $this->language->get('name_klient') . $data['name'] . '<br /><br />' . $this->language->get('telefon_klient') . $data['phone'] . '<br /><br />' . $this->language->get('email_klient') . $data['email'] . '<br /><br />'  . $this->language->get('tovar_klient') . $product_info['name'] . '<br /><br />'. $this->language->get('href_tovar_klient') . $this->url->link('product/product', '&product_id=' . $product_id) . '<br /><br />' . $this->language->get('text_search_cheaper') . $data['href'] . '<br /><br />' . $this->language->get('text_cheaper_comment') . $data['comment'];

			$message  = '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= '    <title>' . $this->language->get('text_cheapering_mail') . $_SERVER["HTTP_HOST"] . '</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>' . html_entity_decode($mess, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
			$message .= '</html>' . "\n";
			
			$store_name = $this->config->get('config_name');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($email);
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get("text_cheapering_mail") . $_SERVER["HTTP_HOST"], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
			
			return $this->language->get('success_send');
		} else {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			return $this->language->get('error_send');
		}
	}
	
	public function writesendquickliveprice($data) {
		if (isset($data['option'])) {$options = " `option` = '" . $this->db->escape(json_encode($data['option'])) . "'";} else {$options = " `option` = '" . 0 . "'";}
		$this->db->query("UPDATE " . DB_PREFIX . "quickpayliveprice SET product_id = '" . (int)$data['product_id'] . "'," . $options . ", qyantity = '" . (int)$data['qyantity'] . "' WHERE id = '1'");
	}
	
	public function getCheaperingliveprice() {
		
		
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."quickpayliveprice"); 

		return $query->rows;
	}
	
	
	public function getOptions($id_option, $product_id, $quantity) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;
				
				$option_data = array();

				foreach (json_decode($id_option) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}
				
		return $option_data;
	}
	
}