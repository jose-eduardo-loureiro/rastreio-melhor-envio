<?php
/**
 * Criações Criativas.
 * Rastreio de pacotes do melhor envio para opencart 3
 */

class ModelExtensionModuleRastreioMelhorEnvio extends Model {

	/**
  	* Creates table for couriers and and trackings
 	* @return [type] [no]
 	*/
	public function createRastreioMelhorEnvioTable() {
   		$this->deleteRastreioMelhorEnvioTable();
   		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "rastreiomelhorenvio_courier` (
     		`rastreiomelhorenvio_courier_id` INT(11) NOT NULL AUTO_INCREMENT,
     		`slug` VARCHAR(255) NOT NULL,
     		`name` VARCHAR(255) NOT NULL,
     		`required_fields` VARCHAR(255),
     		`web_url`  VARCHAR(255) NOT NULL,
     		PRIMARY KEY(`rastreiomelhorenvio_courier_id`), UNIQUE KEY (`slug`)
     		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
     	$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "rastreiomelhorenvio_tracking` (
       		`rastreiomelhorenvio_tracking_id` INT(11) NOT NULL AUTO_INCREMENT,
       		`tracking_id` VARCHAR(255) NOT NULL,
       		`order_id` INT(11) NOT NULL,
       		`slug` varchar(255) NOT NULL DEFAULT '',
       		`tracking_number` VARCHAR(255) NOT NULL DEFAULT '',
       		`comment` TEXT NOT NULL,
       		`date_added` VARCHAR(255) NOT NULL,
       		PRIMARY KEY(`rastreiomelhorenvio_tracking_id`)
       		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
 	}

 	/**
  	* deletes the table of couriers and and trackings
  	*/
 	public function deleteRastreioMelhorEnvioTable() {
   		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "rastreiomelhorenvio_courier`;");
   		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "rastreiomelhorenvio_tracking`;");
 	}

 	/**
  	* @param int [$id, interger rastreiomelhorenvio courier id]
  	* @return array [array of courier information]
  	*/
 	public function getSlug($id) {
   		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_courier` WHERE `rastreiomelhorenvio_courier_id` = '" . (int)$id . "'")->row;
 	}

 	/**
  	* @param string [$slug, courier name]
  	* @return array [array of courier information]
  	*/
 	public function getCourierBySlug($slug) {
   		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_courier` WHERE `slug` = '" . $this->db->escape($slug) . "'")->row;
 	}

 	/**
  	* @param int [$rastreiomelhorenvio_tracking_id]
  	* @return array [array of tracking information]
  	*/
 	public function getTracking($rastreiomelhorenvio_tracking_id) {
   		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `rastreiomelhorenvio_tracking_id` = " . (int)$rastreiomelhorenvio_tracking_id . "")->row;
 	}

 	/**
  	* @param int [$order_id]
  	* @param int [$start]
  	* @param int [$limit]
  	* @return array [array of trackings informations]
  	*/
 	public function getTrackings($order_id, $start, $limit) {
   		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
   		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `order_id` = " . (int)$order_id . " ORDER BY `date_added` DESC LIMIT " . (int)$start . "," . (int)$limit);
   		return $query->rows;
 	}

 	/**
  	* @param int [$order_id]
  	* @param int [$start]
  	* @param int [$limit]
  	* @return int [total number of trackings]
  	*/
 	public function getTotalTrackings($order_id) {
   		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `order_id` = " . (int)$order_id);
   		return count($query->rows);
 	}

 	/**
  	* @param [no param]
  	* @return [no return]
  	* Truncate the courier table, remove all couriers
  	*/
 	public function deleteCouriers() {
   		$sql = "SELECT * FROM `information_schema`.`tables` WHERE `table_schema` = '" . $this->db->escape(DB_DATABASE) . "' AND `table_name` = '" . $this->db->escape(DB_PREFIX) . "rastreiomelhorenvio_courier' LIMIT 1";
   		if ($this->db->query($sql)->row) {
     		$this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "rastreiomelhorenvio_courier`");
   		}
 	}

 	/**
  	* @param [no param]
  	* @return array [get the couriers if the table rastreiomelhorenvio_courier is available]
  	*/
 	public function getRastreioMelhorEnvioCouriers() {
   		$sql = "SELECT * FROM information_schema.tables WHERE `table_schema` = '" . $this->db->escape(DB_DATABASE) . "' AND `table_name` = '" . $this->db->escape(DB_PREFIX) . "rastreiomelhorenvio_courier' LIMIT 1";
   		if ($this->db->query($sql)->row) {
     		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "rastreiomelhorenvio_courier`")->rows;
   		}
   		return array();
 	}

 	/**
	* @param array [$json_couriers, array of couriers]
  * @return no_type
  * Inserts the active counriers available in the database
  */
 	public function insertCouriers() {
      $sql = "SELECT * FROM information_schema.tables WHERE `table_schema` = '" . $this->db->escape(DB_DATABASE) . "' AND `table_name` = '" . $this->db->escape(DB_PREFIX) . "rastreiomelhorenvio_courier' LIMIT 1";
      if ($this->db->query($sql)->row) {
          $query = "INSERT INTO `" . DB_PREFIX . "rastreiomelhorenvio_courier` (`slug`, `name`, `web_url`) VALUES ('melhor-envio-jadlog','Jadlog','https://www.jadlog.com.br');";
     		  $this->db->query($query);
          $query = "INSERT INTO `" . DB_PREFIX . "rastreiomelhorenvio_courier` (`slug`, `name`, `web_url`) VALUES ('melhor-envio-correios','Correios','https://www.correios.com.br');";
          $this->db->query($query);    
      }    
 	}

 	/**
  	* @param array [$tracking_data, array of tracking data]
  	* @param string [$description, comment added by admin while creating tracking]
  	* @return no_type
  	* Inserts the created tracking in the database
  	*/
 	public function createTracking($tracking_data, $description = '') {
   		$track = $tracking_data;
	   	if ($track) {
       		$this->db->query("INSERT INTO `" . DB_PREFIX . "rastreiomelhorenvio_tracking` SET `tracking_id` = '" . $this->db->escape($track['id']) . "',  `tracking_number` = '" . $this->db->escape($track['tracking_number']) . "', `order_id` = " . (int)$track['order_id'] . ", `slug` = '" . $track['slug'] . "', `comment` = '" . $this->db->escape($description) . "', `date_added` = NOW();");
   		}
 	}

 	/**
  	* @param int [$rastreiomelhorenvio_tracking_id]
  	* @return no_type
  	* Deletes a tracking from the database
  	*/
 	public function deleteTracking($rastreiomelhorenvio_tracking_id) {
   		$this->db->query("DELETE FROM `" . DB_PREFIX . "rastreiomelhorenvio_tracking` WHERE `rastreiomelhorenvio_tracking_id` = " . (int)$rastreiomelhorenvio_tracking_id . "");
 	}
}
