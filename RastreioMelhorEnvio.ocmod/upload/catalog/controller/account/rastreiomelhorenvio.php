<?php
/**
 * Webkul Software.
 */

class ControllerAccountRastreioMelhorEnvio extends Controller {
  
  private $error = array();

  public function index() {
    $this->load->language('account/rastreiomelhorenvio');
    $this->document->setTitle($this->language->get('heading_title_rastreiomelhorenvio'));
    $this->document->addStyle('catalog/view/theme/default/stylesheet/rastreiomelhorenvio.css');
    if (!$this->config->get('module_rastreiomelhorenvio_status')) {
      $this->response->redirect($this->url->link('account/account', '', true));
    }
    if (!$this->customer->isLogged()) {
      $this->session->data['redirect'] = $this->url->link('account/rastreiomelhorenvio', '', true);
      $this->response->redirect($this->url->link('account/login', '', true));
    }
    $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

    $data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_rastreiomelhorenvio'),
			'href' => $this->url->link('account/rastreiomelhorenvio', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
    $this->load->model('account/rastreiomelhorenvio');
    $data['couriers'] = $this->model_account_rastreiomelhorenvio->getCouriers();
    $data['action'] = $this->url->link('account/rastreiomelhorenvio/track', '', true);
    $data['header'] = $this->load->controller('common/header');
    $data['footer'] = $this->load->controller('common/footer');
    $data['content_top'] = $this->load->controller('common/content_top');
	  $data['content_bottom'] = $this->load->controller('common/content_bottom');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['column_right'] = $this->load->controller('common/column_right');
    $this->response->setOutput($this->load->view('account/rastreiomelhorenvio', $data));
  }

  public function track() {
  	/*
    $json = array();
    if (!$this->config->get('module_aftership_status')) {
      $json['redirect'] = $this->url->link('account/account', '', true);
    }
    if (!$this->customer->isLogged()) {
      $this->session->data['redirect'] = $this->url->link('account/aftership', '', true);
      $json['redirect'] = $this->url->link('account/login', '', true);
    }
    $this->load->language('account/aftership');
    $this->load->model('account/aftership');
    if (!$this->customer->isLogged()) {
      $json['error_login'] = $this->language->get('error_login');
    }
    $post = $this->request->post;

    if (!$json && (isset($post['tracking_number']) && isset($post['slug']))) {

      $url = 'https://api.aftership.com/v4/trackings';
      $url .= '/' . trim($post['slug']);
      $url .= '/' . trim($post['tracking_number']);

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'aftership-api-key: ' . trim($this->config->get('module_aftership_api_key')),
        'Content-Type: application/json'
      ));

      $param = array();
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      // curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_SSLVERSION, 1);
      // curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $response = json_decode(curl_exec($curl));

      if (isset($response->meta->message)) {
        $json['message'] = $response->meta->message;
      } else if (!$this->model_account_aftership->validateAccess($post['tracking_number'])) {
        $json['error_access'] = $this->language->get('error_access');
      }

      if (isset($response->meta->type) && $response->meta->type == 'BadRequest') {
        $json['message'] = $this->language->get('error_invalid');
      }
      $json['last_tag'] = '';
      $json['tracking_number'] = '';
      $json['subtag_message'] = '';
      $json['checkpoints'] = array();
      if ($response->meta->code == 200 && !isset($json['error_access'])) {
        $courier = $this->model_account_aftership->getCourierBySlug($response->data->tracking->slug);
        $json['courier'] = $courier['name'];
        $json['tracking_number'] = $response->data->tracking->tracking_number;
        $json['subtag_message'] = $response->data->tracking->subtag_message;
        if (isset(end($response->data->tracking->checkpoints)->tag)) {
          $json['last_tag'] = end($response->data->tracking->checkpoints)->tag;
        } else {
          $json['last_tag'] = $response->data->tracking->tag;
        }
        foreach ($response->data->tracking->checkpoints as $checkpoint) {
          $courier = $this->model_account_aftership->getCourierBySlug($checkpoint->slug);
          $json['checkpoints'][] = array(
            'courier'         => $courier['name'],
            'checkpoint_date' => date('M, d Y', strtotime($checkpoint->checkpoint_time)),
            'checkpoint_time' => date('h:i a', strtotime($checkpoint->checkpoint_time)),
            'location'        => $checkpoint->location ? $checkpoint->location : '',
            'message'         => $checkpoint->message,
            'tag'             => $checkpoint->tag
          );
        }
        if ($json['checkpoints']) {
          $json['checkpoints'] = array_reverse($json['checkpoints']);
        }
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
    */
  }
}
