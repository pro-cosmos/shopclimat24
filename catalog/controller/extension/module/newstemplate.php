<?php
class ControllerExtensionModuleNewstemplate extends Controller {
	public function index($setting) {
		static $modul = 0;
		
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		
		$this->load->language('extension/module/newstemplate');

		$data['newss'] = $this->language->get('newss');
		$data['all_news'] = $this->language->get('all_news');
		
		$data['setting'] = $setting;

		$data['modules_news'] = array();

		$this->load->model('extension/module/newstemplate');
		$this->load->model('tool/image');
		
		$modules = $this->model_extension_module_newstemplate->getNewstemplate();
		
		$data['modules_news'] = array();
		
		if ($this->config->get('newstemplate_limit_h1')) {$limit_h1 = $this->config->get('newstemplate_limit_h1');} else {$limit_h1 = '60';}
		if ($this->config->get('newstemplate_limit_description')) {$limit_description = $this->config->get('newstemplate_limit_description');} else {$limit_description = '60';}
		if ($this->config->get('newstemplate_image_width')) {$width = $this->config->get('newstemplate_image_width');} else {$width = '100';}
		if ($this->config->get('newstemplate_image_height')) {$height = $this->config->get('newstemplate_image_height');} else {$height = '100';}
		
		If ($modules) {
			foreach ($modules as $mod) {
				if (empty($mod['news_on'])) {$mod['news_on'] = 0;}
				if (empty($mod['news_sort'])) {$mod['news_sort'] = 0;}
				if (empty($mod['news_date'])) {$mod['news_date'] = date("m/d/y");}
		
				if (empty($mod['news_date'])) {$mod['news_date'] = date("m/d/y");}
				
				if (!empty($mod['news_h1'])) {$decod_name = json_decode($mod['news_h1'], true); $name = utf8_substr(strip_tags(html_entity_decode($decod_name[$this->session->data['language']], ENT_QUOTES, 'UTF-8')), 0, $limit_h1) . '..';} else {$name = false;}
				if (!empty($mod['news_text'])) {$decod_description = json_decode($mod['news_text'], true); $description = $decod_description[$this->session->data['language']];} else {$description = false;}
				
				$data['modules_news'][] = array(
					'news_h1' => $name,
					'news_image' => $this->model_tool_image->resize($mod['news_image'], $width, $height),
					'news_date' => $mod['news_date'],
					'news_sort' => $mod['news_sort'],
					'news_on' => $mod['news_on'],
					'news_id' => $mod['news_id'],
					'news_allhref' => $this->url->link('product/newsall'),
					'news_text' => utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, $limit_description) . '..'					
				);
			}
		}
		
		$data['modul'] = $modul++;

		return $this->load->view('extension/module/newstemplate', $data);
	}
}