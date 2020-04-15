<?php
/**
 * Criações Criativas.
 * Rastreio de pacotes do melhor envio para opencart 3
 */
class ControllerExtensionModuleRastreioMelhorEnvio extends Controller {
	private $error = array();
	public function install() {
    	$this->load->model('extension/module/rastreiomelhorenvio');
    	$this->model_extension_module_rastreiomelhorenvio->createRastreioMelhorEnvioTable();
  	}

  	public function uninstall() {
    	$this->load->model('extension/module/rastreiomelhorenvio');
    	$this->model_extension_module_rastreiomelhorenvio->deleteRastreioMelhorEnvioTable();
  	}

  	public function index() {
    	$this->load->language('extension/module/rastreiomelhorenvio');
    	$this->document->setTitle($this->language->get('heading_title'));
    	$this->load->model('extension/module/rastreiomelhorenvio');
		$this->load->model('setting/setting');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
	  		$this->model_setting_setting->editSetting('module_rastreiomelhorenvio', $this->request->post);
      		if (!$this->model_extension_module_rastreiomelhorenvio->getRastreioMelhorEnvioCouriers()) {
        		$this->model_extension_module_rastreiomelhorenvio->insertCouriers();
      		}
	  		$this->session->data['success'] = $this->language->get('text_success');
	  		$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
    	if (isset($this->error['warning'])) {
	  		$data['error_warning'] = $this->error['warning'];
		} else {
	  		$data['error_warning'] = '';
		}
    	$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
	  		'text' => $this->language->get('text_home'),
	  		'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
		$data['breadcrumbs'][] = array(
	  		'text' => $this->language->get('text_extension'),
	  		'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
		$data['breadcrumbs'][] = array(
	  		'text' => $this->language->get('heading_title'),
	  		'href' => $this->url->link('extension/module/rastreiomelhorenvio', 'user_token=' . $this->session->data['user_token'], true)
			);
		$data['action'] = $this->url->link('extension/module/rastreiomelhorenvio', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
     	if (isset($this->request->post['module_rastreiomelhorenvio_status'])) {
      		$data['module_rastreiomelhorenvio_status'] = $this->request->post['module_rastreiomelhorenvio_status'];
    	} else {
      		$data['module_rastreiomelhorenvio_status'] = $this->config->get('module_rastreiomelhorenvio_status');
    	}
    	$data['user_token'] = $this->session->data['user_token'];
    	$data['header'] = $this->load->controller('common/header');
    	$data['column_left'] = $this->load->controller('common/column_left');
    	$data['footer'] = $this->load->controller('common/footer');
    	$this->response->setOutput($this->load->view('extension/module/rastreiomelhorenvio', $data));
    }

	public function updateCouriers() {
    	$json = array();
    	$this->load->language('extension/module/rastreiomelhorenvio');
    	if ($this->validate()) {
      		$this->load->model('extension/module/rastreiomelhorenvio');
      		$this->model_extension_module_rastreiomelhorenvio->deleteCouriers();
      		$this->model_extension_module_rastreiomelhorenvio->insertCouriers();
      		$json['success'] = $this->language->get('text_update_success');
    	}
    	$this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
  	}
  
  	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/rastreiomelhorenvio')) {
	  		$this->error['warning'] = $this->language->get('error_permission');
		}
    	return !$this->error;
  	}
}
