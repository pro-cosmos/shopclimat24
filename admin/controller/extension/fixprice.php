<?php
class ControllerExtensionFixprice extends Controller {
	private $error = array();
  private $ssl = 'SSL';

  public function __construct( $registry ) {
    parent::__construct( $registry );
    $this->ssl = true;
  }

	public function index() {
		$this->load->language('extension/fixprice');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/fixprice');
		$this->getForm();
	}

  //public function install() {
    //$this->load->model('setting/setting');
    //$this->load->model('setting/module');

   // $this->model_setting_setting->editSetting('fixprice', ['fixprice_status'=>1]);
   // $this->model_setting_module->addModule('fixprice', self::DEFAULT_MODULE_SETTINGS);
  //}



  protected function getForm() {
    $data = array();
    $data['heading_title'] = $this->language->get('heading_title');
    $data['tab_settings'] =  'Settings'; //$this->language->get( 'tab_settings' );
    $data['entry_upload'] = 'Upload'; //$this->language->get( 'entry_upload' );
    $data['button_import'] = 'Import';

    $data['import'] = $this->url->link('extension/fixprice/upload', 'user_token=' . $this->session->data['user_token']);
    $data['count_product'] = 1;//$this->model_extension_export_import->getCountProduct();

    $data['providers'] = [
      'rusklimat' =>
        [
          'key' => 'rusklimat',
          'name' => 'Русклимат',
          'files' => ['catalog', 'price'],
        ],
    ];

    foreach ($data['providers'] as $key => $provider) {
      $path = DIR_APPLICATION . 'uploads/fixprice/' . $key;
      $files = array_diff(scandir($path), ['..', '.']);
      $data['fixprice_result'] = '<ul>' . implode('</li><li>', $files) . '</ul>';

      $path = DIR_APPLICATION . 'uploads/fixprice/' . $key;
      foreach ($provider['files'] as $type) {
        $filename = $path . '/' . date('Y.m.d') . '-' . $type . '.csv';
        if (file_exists($filename)) {
          $data['providers'][$key]['currentfile'][$type] = basename($filename);
        }
      }

      if (!empty($data['providers'][$key]['currentfile']) && count($provider['files']) == count($data['providers'][$key]['currentfile'])){
        $data['providers'][$key]['button_finish_show'] = TRUE;
      }

  }


    $this->document->addStyle('view/stylesheet/export_import.css');
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/fixprice', $data));
  }


  public function upload() {
    $this->load->language('extension/fixprice');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('extension/fixprice');
    $provider = $this->request->post['provider'];


    if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
      // Create the new storage folder
      $path = DIR_APPLICATION . 'uploads/fixprice/' . $provider;
      if (!is_dir($path)) {
        mkdir($path, 0777);
      }

      // Remove files.
      foreach (['catalog', 'price'] as $type) {
        if (!empty($this->request->post['remove_' . $type])) {
          $filename = $path . '/' . date('Y.m.d') . '-' . $type . '.csv';
          if (file_exists($filename)) {
            @unlink($filename);
          }
        }
        else {
          if ((isset($this->request->files['upload_' . $type])) && (is_uploaded_file($this->request->files['upload_' . $type]['tmp_name']))) {
            $file = $this->request->files['upload_' . $type]['tmp_name'];
            $file_realname = $_FILES["upload_".$type]["name"];

            if (substr($file_realname, -3, 3) == 'zip') {
              $zip_file = $file;
              $file = $this->model_extension_fixprice->unzip($file, $path);
              $file_realname = $file;
              @unlink($zip_file);
            }

            if (substr($file_realname, -3, 3) == 'xml') {
              $xml_file = $file;
              $file = $this->model_extension_fixprice->xml2csv($file, $provider, $type);
              @unlink($xml_file);
            }

            if (substr($file_realname, -3, 3) == 'xls') {
              $xls_file = $file;
              $file = $this->model_extension_fixprice->xls2csv($file, $provider, $type);
              @unlink($xls_file);
            }

            if ($file) {
              $this->response->redirect($this->url->link('extension/fixprice', 'user_token=' . $this->session->data['user_token'], $this->ssl));
            }
            else {
              $this->error['warning'] = $this->language->get('error_upload');
            }
          }
        }
      }
    }

    $this->getForm();
  }


	public function download() {
		$this->load->language( 'extension/export_import' );
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model( 'extension/export_import' );
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDownloadForm()) {
			$export_type = $this->request->post['export_type'];
			switch ($export_type) {
				case 'c':
				case 'p':
				case 'u':
					$min = null;
					if (isset( $this->request->post['min'] ) && ($this->request->post['min']!='')) {
						$min = $this->request->post['min'];
					}
					$max = null;
					if (isset( $this->request->post['max'] ) && ($this->request->post['max']!='')) {
						$max = $this->request->post['max'];
					}
					if (($min==null) || ($max==null)) {
						$this->model_extension_export_import->download($export_type, null, null, null, null);
					} else if ($this->request->post['range_type'] == 'id') {
						$this->model_extension_export_import->download($export_type, null, null, $min, $max);
					} else {
						$this->model_extension_export_import->download($export_type, $min*($max-1-1), $min, null, null);
					}
					break;
				case 'o':
					$this->model_extension_export_import->download('o', null, null, null, null);
					break;
				case 'a':
					$this->model_extension_export_import->download('a', null, null, null, null);
					break;
				case 'f':
					if ($this->model_extension_export_import->existFilter()) {
						$this->model_extension_export_import->download('f', null, null, null, null);
						break;
					}
					break;
				default:
					break;
			}
			$this->response->redirect( $this->url->link( 'extension/export_import', 'user_token='.$this->request->get['user_token'], $this->ssl) );
		}

		$this->getForm();
	}


	public function settings() {
		$this->load->language('extension/export_import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/export_import');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateSettingsForm())) {
			if (!isset($this->request->post['export_import_settings_use_export_cache'])) {
				$this->request->post['export_import_settings_use_export_cache'] = '0';
			}
			if (!isset($this->request->post['export_import_settings_use_import_cache'])) {
				$this->request->post['export_import_settings_use_import_cache'] = '0';
			}
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('export_import', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_settings');
			$this->response->redirect($this->url->link('extension/export_import', 'user_token=' . $this->session->data['user_token'], $this->ssl));
		}
		$this->getForm();
	}



	protected function validateDownloadForm() {
		if (!$this->user->hasPermission('access', 'extension/export_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		if (!$this->config->get( 'export_import_settings_use_option_id' )) {
			$option_names = $this->model_extension_export_import->getOptionNameCounts();
			foreach ($option_names as $option_name) {
				if ($option_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $option_name['name'], $this->language->get( 'error_option_name' ) );
					return false;
				}
			}
		}

		if (!$this->config->get( 'export_import_settings_use_option_value_id' )) {
			$option_value_names = $this->model_extension_export_import->getOptionValueNameCounts();
			foreach ($option_value_names as $option_value_name) {
				if ($option_value_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $option_value_name['name'], $this->language->get( 'error_option_value_name' ) );
					return false;
				}
			}
		}

		if (!$this->config->get( 'export_import_settings_use_attribute_group_id' )) {
			$attribute_group_names = $this->model_extension_export_import->getAttributeGroupNameCounts();
			foreach ($attribute_group_names as $attribute_group_name) {
				if ($attribute_group_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $attribute_group_name['name'], $this->language->get( 'error_attribute_group_name' ) );
					return false;
				}
			}
		}

		if (!$this->config->get( 'export_import_settings_use_attribute_id' )) {
			$attribute_names = $this->model_extension_export_import->getAttributeNameCounts();
			foreach ($attribute_names as $attribute_name) {
				if ($attribute_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $attribute_name['name'], $this->language->get( 'error_attribute_name' ) );
					return false;
				}
			}
		}

		if (!$this->config->get( 'export_import_settings_use_filter_group_id' )) {
			$filter_group_names = $this->model_extension_export_import->getFilterGroupNameCounts();
			foreach ($filter_group_names as $filter_group_name) {
				if ($filter_group_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $filter_group_name['name'], $this->language->get( 'error_filter_group_name' ) );
					return false;
				}
			}
		}

		if (!$this->config->get( 'export_import_settings_use_filter_id' )) {
			$filter_names = $this->model_extension_export_import->getFilterNameCounts();
			foreach ($filter_names as $filter_name) {
				if ($filter_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $filter_name['name'], $this->language->get( 'error_filter_name' ) );
					return false;
				}
			}
		}

		return true;
	}


	protected function validateUploadForm() {
	  return true;

		if (!$this->user->hasPermission('modify', 'extension/export_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else if (!isset( $this->request->post['incremental'] )) {
			$this->error['warning'] = $this->language->get( 'error_incremental' );
		} else if ($this->request->post['incremental'] != '0') {
			if ($this->request->post['incremental'] != '1') {
				$this->error['warning'] = $this->language->get( 'error_incremental' );
			}
		}

		if (!isset($this->request->files['upload']['name'])) {
			if (isset($this->error['warning'])) {
				$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
			} else {
				$this->error['warning'] = $this->language->get( 'error_upload_name' );
			}
		} else {
			$ext = strtolower(pathinfo($this->request->files['upload']['name'], PATHINFO_EXTENSION));
			if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
				} else {
					$this->error['warning'] = $this->language->get( 'error_upload_ext' );
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}


	protected function validateSettingsForm() {
		if (!$this->user->hasPermission('access', 'extension/export_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		if (empty($this->request->post['export_import_settings_use_option_id'])) {
			$option_names = $this->model_extension_export_import->getOptionNameCounts();
			foreach ($option_names as $option_name) {
				if ($option_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $option_name['name'], $this->language->get( 'error_option_name' ) );
					return false;
				}
			}
		}

		if (empty($this->request->post['export_import_settings_use_option_value_id'])) {
			$option_value_names = $this->model_extension_export_import->getOptionValueNameCounts();
			foreach ($option_value_names as $option_value_name) {
				if ($option_value_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $option_value_name['name'], $this->language->get( 'error_option_value_name' ) );
					return false;
				}
			}
		}

		if (empty($this->request->post['export_import_settings_use_attribute_group_id'])) {
			$attribute_group_names = $this->model_extension_export_import->getAttributeGroupNameCounts();
			foreach ($attribute_group_names as $attribute_group_name) {
				if ($attribute_group_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $attribute_group_name['name'], $this->language->get( 'error_attribute_group_name' ) );
					return false;
				}
			}
		}

		if (empty($this->request->post['export_import_settings_use_attribute_id'])) {
			$attribute_names = $this->model_extension_export_import->getAttributeNameCounts();
			foreach ($attribute_names as $attribute_name) {
				if ($attribute_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $attribute_name['name'], $this->language->get( 'error_attribute_name' ) );
					return false;
				}
			}
		}

		if (empty($this->request->post['export_import_settings_use_filter_group_id'])) {
			$filter_group_names = $this->model_extension_export_import->getFilterGroupNameCounts();
			foreach ($filter_group_names as $filter_group_name) {
				if ($filter_group_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $filter_group_name['name'], $this->language->get( 'error_filter_group_name' ) );
					return false;
				}
			}
		}

		if (empty($this->request->post['export_import_settings_use_filter_id'])) {
			$filter_names = $this->model_extension_export_import->getFilterNameCounts();
			foreach ($filter_names as $filter_name) {
				if ($filter_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $filter_name['name'], $this->language->get( 'error_filter_name' ) );
					return false;
				}
			}
		}

		return true;
	}


//	public function getNotifications() {
//		sleep(1); // give the data some "feel" that its not in our system
//		$this->load->model('extension/export_import');
//		$this->load->language( 'extension/export_import' );
//		$response = $this->model_extension_export_import->getNotifications();
//		$json = array();
//		if ($response===false) {
//			$json['message'] = '';
//			$json['error'] = $this->language->get( 'error_notifications' );
//		} else {
//			$json['message'] = $response;
//			$json['error'] = '';
//		}
//		$this->response->setOutput(json_encode($json));
//	}
//
//
//	public function getCountProduct() {
//		$this->load->model('extension/export_import');
//		$count = $this->model_extension_export_import->getCountProduct();
//		$json = array( 'count'=>$count );
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
//	}
}
?>