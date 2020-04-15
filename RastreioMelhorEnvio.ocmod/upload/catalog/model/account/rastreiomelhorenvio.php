<?php
/**
 */

class ModelAccountRastreioMelhorEnvio extends Model {
  /**
   * @param array [$data]
   * @return array [$returns message]
   */
   public function createTracking($data = array()) {
    $returns = array();
    if ($this->config->get('module_rastreiomelhorenvio_status')) {
      if (isset($data['tracking_number']) && isset($data['slug']) && isset($data['order_history_id'])) {

        $this->load->language('account/rastreiomelhorenvio');
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `order_id` = " . (int)$data['order_id'] . " AND `slug` = '" . $this->db->escape($data['slug']) . "' AND `tracking_number` = '" . $this->db->escape($data['tracking_number']) . "'")->row;

        if (!$query) {
          $this->load->model('checkout/order');
          $order_info = $this->model_checkout_order->getOrder($data['order_id']);
          $courier = $this->getCourierBySlug($data['slug']);
          $this->db->query("INSERT INTO `" . DB_PREFIX . "rastreiomelhorenvio_tracking` SET `slug` = '" . $this->db->escape($data['slug']) . "', `tracking_number` = '" . $this->db->escape($data['tracking_number']) . "', `order_history_id` = " . (int)$data['order_history_id'] . "");
          
        }
       }
      }
      return $returns;
    }

   /**
   * @param not_type [no param]
   * @return array [list of courier]
   */
   public function getCouriers() {
     return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_courier`")->rows;
   }

   /**
   * @param string [$courier code as slug]
   * @return array[array data of courier]
   */
   public function getCourierBySlug($slug) {
     return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_courier` WHERE `slug` = '" . $this->db->escape($slug) . "'")->row;
   }

   /**
   * @param int [$order_id]
   * @return array[array data of tracking]
   */
   public function getTrackings($order_id) {
    if ($this->config->get('module_rastreiomelhorenvio_status')) {
      return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `order_id` = " . (int)$order_id . "")->rows;
    } else {
        return array();
    }
   }
   /**
   * @param string [$tracking_number]
   * @return int[number of trackings]
   */
   public function validateAccess($tracking_number) {
     $sql = "SELECT DISTINCT customer_id FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "rastreiomelhorenvio_tracking` at ON (o.order_id = at.order_id) WHERE o.customer_id =" . (int)$this->customer->getId() . " AND at.tracking_number = '" . $this->db->escape($tracking_number) . "'";
     return $this->db->query($sql)->num_rows;
   }
}
