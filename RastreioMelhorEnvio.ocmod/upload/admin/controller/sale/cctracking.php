<?php
/**
 * Criações Criativas.
 * Rastreio de pacotes do melhor envio para opencart 3
 */

class ControllerSaleCCtracking extends Controller {
  private $error = array();

  public function index() {
    $data = $this->load->language('sale/cctracking');
    $this->load->model('extension/module/rastreiomelhorenvio');
    $data['couriers'] = $this->model_extension_module_rastreiomelhorenvio->getRastreioMelhorEnvioCouriers();
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
    $this->load->model('sale/order');
    if (isset($this->request->get['order_id'])) {
        $order_id = (int)$this->request->get['order_id'];
    } else {
      $order_id = 0;
    }
    $data['action'] = $this->url->link('sale/cctracking/createtracking', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
  	$results = $this->model_extension_module_rastreiomelhorenvio->getTrackings($order_id, ($page - 1) * 10, 10);
    $data['trackings'] = array();
    foreach ($results as $result) {
      $track = 'https://www.melhorrastreio.com.br/rastreio';
      $tracking_url = '';
      $slug_url = '';      
      if (isset($result['slug']) && $result['slug']) {
        $slug = $this->model_extension_module_rastreiomelhorenvio->getCourierBySlug($result['slug']);
        if ($slug) {
          $slug_url = '<a href="' . $slug['web_url'] . '" target="_blank">' . $slug['name'] . '</a>';
        } else {
          $slug_url = $result['slug'];
        }
      }
      $tracking_url = '';
      if (isset($result['tracking_number']) && $result['tracking_number']) {
        if (is_array(explode(',', $result['tracking_number']))) {
          foreach(explode(',', $result['tracking_number']) as $tracking_number) {
            $tracking_url .= '<a href="' . $track . '/' . $tracking_number .'" target="_blank">' . $tracking_number . '</a>,';
          }
        } else {
          $tracking_url = '<a href="' . $track . '" target="_blank">' . $result['tracking_number'] . '</a>';
        }
      } else {
        $tracking_url = '';
      }
      $data['trackings'][] = array(
      'id'          => $result['rastreiomelhorenvio_tracking_id'],
      'tracking'    => rtrim($tracking_url, ','),
      'slug'        => $slug_url,
      'date_added'  => $result['date_added'],
      'delete'      => $this->url->link('sale/cctracking/deletetracking&rastreiomelhorenvio_tracking_id=' . $result['rastreiomelhorenvio_tracking_id'], 'user_token=' . $this->session->data['user_token'], true),
      'delete'      => $this->url->link('sale/cctracking/deletetracking&rastreiomelhorenvio_tracking_id=' . $result['rastreiomelhorenvio_tracking_id'], 'user_token=' . $this->session->data['user_token'], true),
      'info'        => $this->url->link('sale/cctracking/info&rastreiomelhorenvio_tracking_id=' . $result['rastreiomelhorenvio_tracking_id'], 'user_token=' . $this->session->data['user_token'], true)
      );
    }
    $total = $this->model_extension_module_rastreiomelhorenvio->getTotalTrackings($order_id);

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/cctracking', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($total - 10)) ? $total : ((($page - 1) * 10) + 10), $total, ceil($total / 10));
    if (isset($this->request->get['tracking_table'])) {
      $this->response->setOutput($this->load->view('sale/cctracking', $data));
    } else {
      $this->response->setOutput($this->load->view('sale/cctracking_form', $data));
    }
  }

  public function createTracking() {
    $json = array();
    $json['error'] = '';
    $json['success'] =  '';

    $this->load->language('sale/cctracking');
    
    if (isset($this->request->post['tracking_numbers']) && $this->request->post['tracking_numbers']) {
      $description = isset($this->request->post['description']) ? $this->request->post['description'] : $this->language->get('text_tracking_add');;
      $tracking_numbers = array_unique(explode(',', $this->request->post['tracking_numbers']));
      $this->load->model('sale/order');
      $this->load->model('extension/module/rastreiomelhorenvio');
      $order_info = $this->model_sale_order->getOrder((int)$this->request->get['order_id']);

      $slug = $this->model_extension_module_rastreiomelhorenvio->getSlug($this->request->post['slug']);
      foreach ($tracking_numbers as $tracking_number) {
        $mytracking = array();
        $mytracking['id']               = uniqid();
        $mytracking['tracking_number']  = trim($tracking_number);
        $mytracking['order_id']         = $order_info['order_id'];
        $mytracking['slug']             = $slug['slug'];
        $this->model_extension_module_rastreiomelhorenvio->createTracking($mytracking, $description);
        $json['success'] = $this->language->get('text_success_tracking');
      }
    } else {
      $json['error'] = $this->language->get('error_tracking');
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }



  public function deleteTracking() {
    $json = array();
    if (isset($this->request->get['rastreiomelhorenvio_tracking_id'])) {
      $this->load->model('extension/module/rastreiomelhorenvio');
      $this->load->language('sale/cctracking');
      $tracking = $this->model_extension_module_rastreiomelhorenvio->getTracking($this->request->get['rastreiomelhorenvio_tracking_id']);
      if ($tracking && isset($tracking['tracking_id'])) {
        $this->model_extension_module_rastreiomelhorenvio->deleteTracking($tracking['rastreiomelhorenvio_tracking_id']);
        $json['error'] = '';
        $json['success'] = $this->language->get('text_success_delete');
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'sale/cctracking')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
  }
}
