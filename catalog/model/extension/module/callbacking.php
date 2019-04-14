<?php
class ModelExtensionModuleCallbacking extends Model {
	public function createcallbacking() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."callbacking'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "callbacking` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `date` varchar(255) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `phone` varchar(255) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `comment` longtext NOT NULL,
				  `status` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function writesendquick($data) {
		
		$this->load->language('extension/module/langtemplates');
		
		if($this->db->query("INSERT INTO " . DB_PREFIX . "callbacking SET date = '" . $this->db->escape($data['date']) . "', name = '" . $this->db->escape($data['name']) . "', phone = '" . $this->db->escape($data['phone']) . "', email = '" . $this->db->escape($data['email']) . "', comment = '" . $this->db->escape($data['comment']) . "', status = '0'")) {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			
			if (isset($data['email'])) {
				$email = $data['email'];
			} else {
				$email = $this->config->get('config_email');
			}
			
			
			$mess = $this->language->get('name_klient') . $data['name'] . '<br /><br />' . $this->language->get('date_klient') . $data['date'] . '<br /><br />' . $this->language->get('telefon_klient') . $data['phone'] . '<br /><br />'  . $this->language->get('email_klient') . $data['email'] . '<br /><br />'  . $this->language->get('comment_klient') . $data['comment'] . '<br /><br />';

			$message  = '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= '    <title>' . $this->language->get("text_callbacking") . '</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>' . html_entity_decode($mess, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
			$message .= '</html>' . "\n";

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
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get("text_callbacking") . $_SERVER["HTTP_HOST"], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
			
			return $this->language->get('success_send');
		} else {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			return $this->language->get('error_send');
		}
	}

	
}