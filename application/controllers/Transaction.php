<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Transaction_model');
		date_default_timezone_set('Asia/Jakarta');
	}

	public function auto_fill()
   	{
   		if ($this->session->userdata('logged_in')==TRUE) {
	   		$num=(!empty($_POST["sku_barang"]))?$_POST["sku_barang"]:die('Search is empty');

	   	 	$scan_data =  $this->input->post('sku_barang');

	   	 	$query = $this->Transaction_model->get_scan_barang($scan_data);

				if ($num == $scan_data)
				{
					foreach ($query as $value) {
					 $fill = array(
					 	'id_barang' => $value->id_barang,
					 	'sku_barang' => $value->sku_barang,
				    	'nama_barang' => $value->nama_barang,
				    	'harga_modal' => $value->harga_modal,
				    	'harga_jual' => $value->harga_jual,
				    	'stok' => $value->stok,
				    	'satuan' => $value->satuan
				    	);
					}
				    echo json_encode($fill);
				}
				else
				{
				    echo "Bad input";
				}
   		}else{
   			redirect('admin','refresh');
   		}

   	}

   	public function add_barang()
   	 {
   	 	if($this->session->userdata('logged_in') == TRUE){
			// $this->form_validation->set_rules('sku_barang', 'SKU', 'trim|required');
			// $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'trim|required');
			// $this->form_validation->set_rules('harga_modal', 'Harga Modal', 'trim|required');
			// $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'trim|required');
			// $this->form_validation->set_rules('stok', 'Stok', 'trim|required');
			
			// if ($this->form_validation->run() == TRUE) {
				if($this->Transaction_model->add_barang() == TRUE){
					$this->session->set_flashdata('berhasil', 'Berhasil menambahkan barang baru');
					redirect('admin/to_barang');
				}else{
					$this->session->set_flashdata('gagal', 'Gagal menambahkan barang baru');
					redirect('admin/to_barang');
				}
			// } else {
			// 	$this->session->set_flashdata('gagal', 'Gagal,cek kembali form anda');
			// 	// redirect('admin/to_barang');
			// }
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }
   	 }

	public function update_barang()
   	{
   		if($this->session->userdata('logged_in') == TRUE){
	   	 	if($this->Transaction_model->update_barang() == TRUE){
	   	 		$this->session->set_flashdata('berhasil', 'Berhasil update barang');
	   	 		redirect('admin/barang');
	   	 	}else{
	   	 		$this->session->set_flashdata('gagal', 'Gagal update barang');
	   	 		redirect('admin/to_barang');
	   	 	}
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }
   	}

   	public function simpan_retur_penjualan()
   	{
   		if($this->session->userdata('logged_in') == TRUE){
	   	 	if($this->Transaction_model->simpan_retur_penjualan() == TRUE){
	   	 		$this->session->set_flashdata('berhasil', 'Tambah retur penjualan berhasil!');
	   	 		redirect('admin/to_retur_penjualan/'.$this->uri->segment(3));
	   	 	}else{
	   	 		$this->session->set_flashdata('gagal', 'Tambah return penjualan gagal!');
	   	 		redirect('admin/to_retur_penjualan/'.$this->uri->segment(3));
	   	 	}
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }
   	}

   	public function update_retur_penjualan($id_retur_penjualan)
   	{
   		if($this->session->userdata('logged_in') == TRUE){
	   	 	if($this->Transaction_model->update_retur_penjualan($id_retur_penjualan) == TRUE){
	   	 		$this->session->set_flashdata('berhasil', 'Ubah retur penjualan berhasil!');
	   	 		redirect('admin/to_update_retur_penjualan/'.$this->uri->segment(3));
	   	 	}else{
	   	 		$this->session->set_flashdata('gagal', 'Ubah return penjualan gagal!');
	   	 		redirect('admin/to_update_retur_penjualan/'.$this->uri->segment(3));
	   	 	}
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }
   	}

   	public function hapus_retur_penjualan($id_retur_penjualan)
   	{
   		if($this->session->userdata('logged_in')){
   			$id_penjualan = $this->db->query('SELECT id_penjualan FROM tb_retur_penjualan WHERE id_retur_penjualan='.$id_retur_penjualan)->row();
   			if($this->Transaction_model->hapus_retur_penjualan($id_retur_penjualan)){
   				$this->session->set_flashdata('berhasil', 'Hapus return penjualan berhasils!');
	   	 		redirect('admin/to_retur_penjualan/'.$id_penjualan);
   			}else{
   				$this->session->set_flashdata('gagal', 'Hapus return penjualan gagal!');
	   	 		redirect('admin/to_retur_penjualan/'.$id_penjualan);
   			}
   		}else{
   			redirect('admin','refresh');
   		}
   	}

   	public function simpan_retur_pembelian()
   	{
   		if($this->session->userdata('logged_in') == TRUE){
	   	 	if($this->Transaction_model->simpan_retur_pembelian() == TRUE){
	   	 		$this->session->set_flashdata('berhasil', 'Simpan retur pembelian berhasil!');
	   	 		redirect('admin/to_retur_pembelian/'.$this->uri->segment(3));
	   	 	}else{
	   	 		$this->session->set_flashdata('gagal', 'Simpan retur pembelian gagal!');
	   	 		redirect('admin/to_retur_pembelian/'.$this->uri->segment(3));
	   	 	}
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }

   	}

   	public function update_retur_pembelian($id_retur_pembelian)
   	{
   		if($this->session->userdata('logged_in') == TRUE){
	   	 	if($this->Transaction_model->update_retur_pembelian($id_retur_pembelian) == TRUE){
	   	 		$this->session->set_flashdata('berhasil', 'Ubah retur pembelian berhasil!');
	   	 		redirect('admin/to_update_retur_pembelian/'.$this->uri->segment(3));
	   	 	}else{
	   	 		$this->session->set_flashdata('gagal', 'Ubah return pembelian gagal!');
	   	 		redirect('admin/to_update_retur_pembelian/'.$this->uri->segment(3));
	   	 	}
	   	 }else{
	   	 	redirect('admin','refresh');
	   	 }
	}

	public function get_barang_stock_opname()
	{
		if($this->session->userdata('logged_in')){
			
			$scan_data = $this->input->post('sku_barang');
			$result = $this->Transaction_model->get_scan_barang($scan_data);

			if(!empty($result)){
				foreach($result as $row){
					if($row->stok > 0 || 1 == 1){ //NO NEED TO CHECK STOCK?
						echo '<li>
								<a class="list" style="display:block;cursor:pointer" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_jual.'" data-produkstok="'.$row->stok.'" onclick="add_barang_stock_opname(this);">
								<div class="row">
									<div class="col-sm-6">
									' . $row->nama_barang . '
									</div>
									<div class="col-sm-6">
									<button type="button" class="add_cart btn btn-success btn-sm" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_jual.'" data-produkstok="'.$row->stok.'" onclick="add_barang_stock_opname(this);"><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</a>
						</li>';
					}else{
						echo "";
					}
				}
			}else{
				echo '<li> Barang tidak ditemukan </li>';
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function add_stock_opname_run()
	{
		if($this->session->userdata('logged_in')){
			
			//$this->form_validation->set_rules('sku_barang', 'SKU*', 'trim|required');
			$this->form_validation->set_rules('id_barang', '', 'trim|required');
			$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'trim|required');
			$this->form_validation->set_rules('stok', '', 'trim|required');
			$this->form_validation->set_rules('stok_gudang', 'Stok Gudang', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
				if($this->Transaction_model->add_stock_opname() == TRUE){
					$this->session->set_flashdata('berhasil', 'Stok berhasil diperbaharui');
					redirect('admin/add_stock_opname');
				}else{
					$this->session->set_flashdata('gagal', 'Stok gagal diperbaharui');
					redirect('admin/add_stock_opname');
				}
			} else {
				$this->session->set_flashdata('gagal', 'Gagal,cek kembali form anda');
				redirect('admin/add_stock_opname');
			}	
		}else{
			redirect('admin','refresh');
		}
	}

	public function edit_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->edit_penjualan($id_penjualan)){
				$this->session->set_flashdata('berhasil', 'Penjualan berhasil diperbarui');
				redirect('admin/penjualan','refresh');
			}else{
				$this->session->set_flashdata('gagal', 'Penjualan gagal diperbarui');
				redirect('admin/detail_penjualan/'.$this->uri->segment(3),'refresh');
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function edit_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->edit_pembelian($id_pembelian)){
				$this->session->set_flashdata('berhasil', 'Pembelian berhasil diperbarui');
				redirect('admin/pembelian','refresh');
			}else{
				$this->session->set_flashdata('gagal', 'Pembelian gagal diperbarui');
				redirect('admin/detail_pembelian/'.$this->uri->segment(3),'refresh');
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function add_detail_stock_opname_baru_run($id_stock_opname_baru)
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->add_detail_stock_opname_baru_run($id_stock_opname_baru)){
				$this->session->set_flashdata('berhasil', 'Stock Opname berhasil disimpan');
				redirect('admin/detail_stock_opname_baru/'.$this->uri->segment(3),'refresh');
			}else{
				$this->session->set_flashdata('gagal', 'Stock Opname gagal disimpan');
				redirect('admin/add_detail_stock_opname_baru/'.$this->uri->segment(3),'refresh');
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function add_supplier()
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->add_supplier() == TRUE){
				$data = array(
					'msg' => 'Supplier baru berhasil ditambahkan',
					'valid' => TRUE
					 );
			}else{
				$data = array(
					'msg' => 'Supplier baru gagal ditambahkan',
					'valid' => FALSE
				);
			}
			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function add_pelanggan()
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->add_pelanggan() == TRUE){
				$data = array(
					'msg' => 'Pelanggan baru berhasil ditambahkan',
					'valid' => TRUE
					 );
			}else{
				$data = array(
					'msg' => 'Pelanggan baru gagal ditambahkan',
					'valid' => FALSE
				);
			}
			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function add_payment()
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->add_payment() == TRUE){
				$data = array(
					'msg' => 'Payment baru berhasil ditambahkan',
					'valid' => TRUE
					 );
			}else{
				$data = array(
					'msg' => 'Payment baru gagal ditambahkan',
					'valid' => FALSE
				);
			}
			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function quick_add_barang()
	{
		if($this->session->userdata('logged_in')){
			if($this->Transaction_model->add_barang() == TRUE){
				$data = array(
					'msg' => 'Barang baru berhasil ditambahkan',
					'valid' => TRUE
					 );
			}else{
				$data = array(
					'msg' => 'Barang baru gagal ditambahkan',
					'valid' => FALSE
				);
			}
			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}
}

/* End of file Transaction.php */
/* Location: ./application/controllers/Transaction.php */
