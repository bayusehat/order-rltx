<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tambah extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Transaction_model');
	}
	public function get_barang_tambah()
	{
		if ($this->session->userdata('logged_in') == TRUE) {
			$scan_data = $this->input->post('search_data_tambah');

			$result = $this->Transaction_model->get_scan_barang($scan_data);

	        if (!empty($result))
	          	{
	               foreach ($result as $row){
	               	if($row->stok > 0 || 1 == 1){ //no need pengecekan stock
	               		if($row->deleted == 0){
							echo '
								<li>
		               			<a class="list" style="display:block;cursor:pointer" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_jual.'" data-produkhargamodal="'.$row->harga_modal.'" onclick="add_barang(this);">
					                <div class="row">
						                <div class="col-sm-6">
						               ' . $row->nama_barang . '
						                </div>
						                <div class="col-sm-6">
					                		<button type="button" class="add_cart btn btn-success btn-sm" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_jual.'" data-produkhargamodal="'.$row->harga_modal.'" data-jual="'.base_url().'index.php/admin/to_penjualan" data-beli="'.base_url().'index.php/admin/to_pembelian" onclick="add_barang(this);"><i class="fa fa-plus"></i></button>
					                		<input type="number" class="quantity input-sm qty" name="quantity" id="'.$row->id_barang.'" value="1" min="0" />
				                		</div>
			                		</div>
		                		</a>
											</li>';
	                	}else{
	                		echo "";
	                	}

	                }else{
	                	echo "<li> <em> Not found ... </em> </li>";
	                	}
	            	}

	            }else{
	                echo "<li> <em> Not found ... </em> </li>";
	            }
		}else{
			redirect('admin','refresh');
		}
	}

}

/* End of file Tambah.php */
/* Location: ./application/controllers/Tambah.php */