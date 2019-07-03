<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

	public function get_penjualan_not_fix()
	{
		return $this->db->where('status','Belum Terbayar')
						->get('tb_penjualan',3)
						->result();
	}

	public function get_pembelian_not_fix()
	{
		return $this->db->where('status','Belum Terbayar')
						->get('tb_pembelian',3)
						->result();
	}

}

/* End of file Notification_model.php */
/* Location: ./application/models/Notification_model.php */