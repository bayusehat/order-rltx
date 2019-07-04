<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('User','Transaction_model','Notification_model'));
		date_default_timezone_set('Asia/Jakarta');
	}

	public function login()
	{
		$data = $this->User->login();
		echo json_encode($data);
	}

	public function logout()
	{
		$data = array(
			'username' => '' ,
			'logged_in' => FALSE
		);

		$this->session->sess_destroy($data);
		redirect('admin');
	}

	public function index()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Dashboard';
			$data['jual'] = $this->Transaction_model->get_penjualan_stats();
			$data['beli'] = $this->Transaction_model->get_pembelian_stats();
			$data['sales'] = $this->Transaction_model->get_sales_progress();
			$data['supplier'] = $this->Transaction_model->get_supplier_progress();
			$this->load->view('dashboard',$data);
		}else{
			$this->load->view('login');
		}
	}

	public function user()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('User');
			$c->set_table('tb_user');
			$c->where('deleted',0);
			$c->order_by('id_user','ASC');
			$c->unset_edit();
			$c->unset_columns('created','updated','deleted','confirm_password');
			$c->unset_fields('created','updated','deleted');
			$c->field_type('password','password');
			$c->required_fields('username','password','nama','email','level');
			$c->callback_before_insert(array($this,'encrypt_password'));
			$c->callback_delete(array($this,'delete_user'));
			$c->callback_column('level',array($this,'level_format'));
			$c->add_action('Edit', '', 'admin/to_edit_user', 'fa-user');
			if($c->getState() == "insert_validation") {
			    $c->set_rules('password', 'Password', 'required');
			    $c->set_rules('confirm_password', 'Confirm Password', "trim|required|matches[password]");
			}
			$title = 'Data User';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_user', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_user($primary_key)
	{
		return $this->db->update('tb_user',array('deleted' => '1'),array('id_user'=>$primary_key));
	}

	public function encrypt_password($post_array)
	{
		$post_array['password'] = md5($post_array['password']);
		$post_array['confirm_password'] = md5($post_array['confirm_password']);

		return $post_array;
	}

	public function to_edit_user($id_user)
	{
		if($this->$this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Edit User';
			$data['edit'] = $this->User->to_edit_user($id_user);
			$this->load->view('edit_user', $data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function edit_user()
	{
		if($this->session->userdata('logged_in') == TRUE){
			if($this->session->userdata('level') == 'admin'){
				if($this->User->edit_user() == TRUE){
						$this->session->set_flashdata('berhasil', 'Edit User berhasil');
						redirect('admin/user');
				}else{
					$this->session->set_flashdata('gagal', 'Edit User gagal');
					redirect('admin/to_edit_user/'.$this->uri->segment(3));
				}
			}else{
				redirect('admin','refresh');
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function change_password()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data = $this->User->change_password();
			if($data == TRUE){
				echo json_encode($data);
			}else{
				return false;
			}
		}else{
			redirect('admin','refresh');
		}
	}

	public function level_format($value,$row)
	{
		if($row->level == 'admin'){
			return '<div class="label label-success">'.$value.'</div>';
		}else{
			return '<div class="label label-info">'.$value.'</div>';
		}
	}

	public function barang()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Barang');
			$c->where('deleted',0);
			$c->order_by('id_barang','DESC');
			$c->set_table('tb_barang');
			$c->display_as('kode_barang','Kode')
			  ->display_as('nama_barang','Barang')
			  ->display_as('harga_modal','Harga Modal')
			  ->display_as('harga_jual','Harga Jual')
			  ->display_as('sku_barang','SKU Barang');
			$c->unset_columns('created','updated','deleted');
			$c->unset_add();
			$c->unset_fields('created','updated','deleted');
			$c->required_fields('kode_barang','nama_barang','harga_jual','harga_modal','stok');
			$c->callback_column('harga_modal',array($this,'format'))
			  ->callback_column('harga_jual',array($this,'format'));
			$c->callback_after_insert(array($this,'history_barang_baru'));
			//$c->callback_update(array($this,'update_stok'));
			$c->callback_update(array($this,'update_harga'));
			$c->callback_delete(array($this,'delete_barang'));
			$c->add_action('Kartu Stok', '', 'admin/kartu_stok', 'fa-file');
			$title = 'Data Barang';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_barang', $output);
		}else{
			redirect('admin');
		}
	}

	public function delete_barang($primary_key)
	{
		return $this->db->update('tb_barang',array('deleted' => '1'),array('id_barang'=>$primary_key));
	}

	public function update_stok($post_array,$primary_key)
	{
		if($this->session->userdata('logged_in')){

			$stokawal = $this->Transaction_model->get_stok($primary_key);
		
			$stokakhir = $post_array['stok'];
			
			$stokdelta = $stokakhir - $stokawal;
			
			$this->db->update('tb_barang', $post_array, array('id_barang' => $primary_key));
			
			if($stokdelta != 0){
				$history = array(
						'id_barang' => $primary_key,
						'mod_stok' => $stokdelta,
						'tanggal' => date('Y-m-d H:i:s'),
						'keterangan' => 'Perubahan stok Master' 
					);
		
					$this->db->insert('tb_history_stok',$history);
				}
		}else{
			redirect('admin','refresh');
		}
	}

	public function update_harga($post_array,$primary_key)
	{
		$harga = $this->Transaction_model->get_harga($primary_key)->row();
		$stok = $this->Transaction_model->get_stok($primary_key);

		$harga_modal = $harga->harga_modal;
		//$harga_jual = $harga->harga_jual;

		$new_harga_modal = $post_array['harga_modal'];
		//$new_harga_jual = $post_array['harga_jual'];

		$stok_awal = $stok->stok;
		
		$new_stok = $post_array['stok'];
		
		if($harga_modal != $new_harga_modal){
			$h_harga_modal = $new_harga_modal-$harga_modal;

			$history = array(
				'id_barang' =>$primary_key,
				'mod_harga' => $new_harga_modal,
				'tanggal' => date('Y-m-d H:i:s'),
				'keterangan' => 'Perubahan Harga Modal via Master Barang' 
			);
			$this->db->insert('tb_history_harga_barang', $history);
		}

		if($new_stok != $stok_awal){
			
        	$history = array(
        		'id_barang' => $primary_key,
        		'mod_stok' => $new_stok - $stok_awal,
        		'tanggal' => date('Y-m-d h:i:s'),
        		'keterangan' => 'Perubahan Stok via Master Barang' 
        	);

        	$this->db->insert('tb_history_stok',$history);
        }

		$this->db->update('tb_barang', $post_array,array('id_barang'=>$primary_key));

		/*if($harga_jual != $new_harga_jual){
			$h_harga_jual = $new_harga_jual-$harga_jual;
			
			$this->db->update('tb_barang', $post_array,array('id_barang'=>$primary_key));

			$history = array(
				'id_barang' =>$primary_key,
				'mod_harga' => $h_harga_jual,
				'tanggal' => date('Y-m-d H:i:s'),
				'keterangan' => 'Perubahan harga jual' 
			);
			$this->db->insert('tb_history_harga_barang', $history);
		}*/


	}

	public function pelanggan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Pelanggan');
			$c->where('deleted',0);
			$c->set_table('tb_pelanggan');
			$c->display_as('nama_pelanggan','Pelanggan')
			  ->display_as('nama_perusahaan','Perusahaan')
			  ->display_as('alamat_pelanggan','Alamat')
			  ->display_as('kota_pelanggan','Kota')
			  ->display_as('nomor_telepon_pelanggan','No. Telepon')
			  ->display_as('email_pelanggan','E-mail');
			$c->unset_columns('created','updated','deleted');
			$c->unset_texteditor('alamat_pelanggan','fulltext');
			$c->unset_fields('deleted','created','updated');
			$c->required_fields('nama_pelanggan','nama_perusahaan','alamat_pelanggan','kota_pelanggan','nomor_telepon_pelanggan','email_pelangganr');
			$c->callback_delete(array($this,'delete_pelanggan'));
			$title = 'Data Pelanggan';
			$this->load->vars( array('title' => $title));

			$output = $c->render();
			$this->load->view('data_pelanggan', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_pelanggan($primary_key)
	{
		return $this->db->update('tb_pelanggan',array('deleted' => '1'),array('id_pelanggan'=>$primary_key));
	}

	public function sales()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Sales');
			$c->where('deleted',0);
			$c->set_table('tb_sales');
			$c->display_as('nama_sales','Sales')
			  ->display_as('nama_perusahaan','Perusahaan')
			  ->display_as('alamat_sales','Alamat')
			  ->display_as('kota_sales','Kota')
			  ->display_as('nomor_telepon_sales','No. Telepon')
			  ->display_as('email_sales','E-mail');
			$c->unset_columns('created','updated','deleted');
			$c->unset_texteditor('alamat_sales','fulltext');
			$c->unset_fields('deleted','created','updated');
			$c->required_fields('nama_sales','nama_perusahaan','alamat_sales','kota_sales','nomor_telepon_sales','email_sales');
			$c->callback_delete(array($this,'delete_sales'));
			$title = 'Data Sales';
			$this->load->vars( array('title' => $title));

			$output = $c->render();
			$this->load->view('data_sales', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_sales($primary_key)
	{
		return $this->db->update('tb_sales',array('deleted' => '1'),array('id_sales'=>$primary_key));
	}

	public function supplier()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Supplier');
			$c->where('deleted',0);
			$c->set_table('tb_supplier');
			$c->display_as('nama_supplier','Supplier')
			  ->display_as('nama_perusahaan','Perusahaan')
			  ->display_as('alamat_supplier','Alamat')
			  ->display_as('kota_supplier','Kota')
			  ->display_as('nomor_telepon_supplier','No. Telepon')
			  ->display_as('email_supplier','E-mail');
			$c->unset_columns('created','updated','deleted');
			$c->unset_texteditor('alamat_supplier','fulltext');
			$c->unset_fields('deleted','created','updated');
			$c->required_fields('nama_supplier','nama_perusahaan','alamat_supplier','kota_supplier','nomor_telepon_supplier','email_supplier');
			$c->callback_delete(array($this,'delete_supplier'));
			$title = 'Data Supplier';
			$this->load->vars( array('title' => $title));

			$output = $c->render();
			$this->load->view('data_supplier', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_supplier($primary_key)
	{
		return $this->db->update('tb_suppler',array('deleted' => '1'),array('id_supplier'=>$primary_key));
	}

	public function penjualan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Penjualan');
			$c->set_table('tb_penjualan');
			$c->where('tb_penjualan.deleted',0);
			$c->unset_edit();
			$c->unset_read();
			$c->order_by('id_penjualan','DESC');
			$c->columns('nomor_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','total','total_retur','status','tindakan');
			$c->display_as('nomor_penjualan','Nomor')
			  ->display_as('tanggal_penjualan','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_pelanggan','Pelanggan')
			  ->display_as('id_sales','Sales')
			  ->display_as('ppn','PPN')
			  ->display_as('total_retur','Total Retur');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->set_relation('id_sales','tb_sales','nama_sales')
			  ->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_pelanggan','tb_pelanggan','nama_pelanggan');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_penjualan'))
		      ->callback_column('ppn',array($this,'ppn_tag'))
		      ->callback_column('total_retur',array($this,'total_retur_penjualan'))
		      ->callback_column('status',array($this,'status_tag'));
		    $c->add_action('Detail', '', 'admin/detail_penjualan', 'fa-file');
		    $c->add_action('Cetak Nota', '', 'admin/print_detail_penjualan', 'fa-file');
		    $c->add_action('Lihat Nota', '', 'admin/cetak_detail_penjualan', 'fa-print');
		    $c->callback_delete(array($this,'delete_penjualan'));
		    $title = 'Data Penjualan';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_penjualan', $output);
		}else{
			redirect('admin','refresh');
		}
	}
	
	public function delete_penjualan($primary_key)
	{
		$currentnota = $this->db->where('id_penjualan', $primary_key)->where('deleted', 0)->from('tb_penjualan')->get()->row();
		if(count($currentnota) < 1) return false;
		
		$id_penjualan = $currentnota->id_penjualan;
		
		$delete_result = $this->db->update('tb_penjualan', array('deleted'=>'1'),array('id_penjualan'=>$primary_key));
		
		if($delete_result != false){
			$get_detail_nota = $this->db->query("select * from tb_detail_penjualan where deleted = 0 and id_penjualan = $id_penjualan")->result();
			
			foreach($get_detail_nota as $detailnota){
				//REVERSE ARUS STOK
				$this->db->query("UPDATE tb_barang SET stok = stok - ".$detailnota->jumlah." WHERE id_barang=".$detailnota->id_barang);	
					
				//$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok - ".$detailnota->jumlah." WHERE id_pembelian = ".$id_pembelian." AND id_barang=".$detailnota->id_barang);	
				$this->db->query("UPDATE tb_history_stok SET deleted = 1 WHERE id_penjualan = ".$id_penjualan." AND id_barang=".$detailnota->id_barang);	
				//decided to just delete the history rather than modifying the mod_stok
			}	
			
		}
		
		return $delete_result;
	}

	public function edit_detail_penjualan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();
			$query = $this->db->query('SELECT * FROM tb_penjualan WHERE id_penjualan='.$this->uri->segment(3))->row();
			$c->set_subject('Detail Penjualan '.$query->nomor_penjualan);
			$c->set_table('tb_detail_penjualan');
			$c->where('tb_detail_penjualan.deleted',0);
			$c->where('tb_detail_penjualan.id_penjualan',$this->uri->segment(3));
			$c->order_by('id_detail_penjualan','DESC');
			// $c->columns('id_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','total','total_retur','status','tindakan');
			$c->display_as('id_penjualan','Penjualan')
			  ->display_as('sku_barang','SKU')
			  ->display_as('nama_barang','Barang')
			  ->display_as('diskon', 'Diskon (%)');
			$c->field_type('id_penjualan', 'readonly')
				->field_type('sku_barang', 'readonly')
				->field_type('nama_barang', 'readonly');
			$c->unset_columns('id_barang','created','updated','deleted');
			$c->unset_fields('id_barang','deleted','created','updated', 'subtotal');
			$c->unset_add();
			$c->unset_read();
			$c->set_relation('id_penjualan','tb_penjualan','nomor_penjualan');
			$c->callback_column('harga',array($this,'format'))
			  ->callback_column('subtotal',array($this,'format'));
			$c->callback_update(array($this,'update_detail_penjualan'));
			$c->callback_delete(array($this,'delete_detail_penjualan'));
		    $title = 'Data Detail Penjualan';
		 	$this->load->vars(array('title' => $title, 'state' => $c->getState()));

			$output = $c->render();
			$this->load->view('data_detail_penjualan', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function update_detail_penjualan($post_array,$primary_key)
	{
		$harga = $post_array['harga'];
		$diskon = $post_array['diskon'];
		$qty = $post_array['jumlah'];
		$subtotal = ($harga - ($harga * ($diskon / 100))) * $qty;

		$current = $this->db->where('id_detail_penjualan', $primary_key)->where('deleted', 0)->from('tb_detail_penjualan')->get()->row();
		if(count($current) < 1) return false;

		$id_penjualan = $current->id_penjualan;

		$data = array(
			'harga' => $harga,
			'diskon' => $diskon,
			'jumlah' => $qty,
			'subtotal' => $subtotal 
		);

		$this->db->update('tb_detail_penjualan',$data,array('id_detail_penjualan'=>$primary_key));

		// Generate new Total
		$new_total = $this->db->query('SELECT SUM(subtotal) as total FROM tb_detail_penjualan WHERE deleted = 0 AND id_penjualan='.$id_penjualan)->result();

		foreach ($new_total as $data) {
			$total = $data->total;
		}
		$this->db->update('tb_penjualan',array('total' => $total),array('id_penjualan' => $id_penjualan));

		//REVERSE ARUS STOK
		if($current->jumlah != $qty){
			$delta = $qty - $current->jumlah;
			$this->db->query("UPDATE tb_barang SET stok = stok - $delta WHERE id_barang=".$current->id_barang);	
			
			$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok - $delta WHERE id_penjualan = ".$current->id_penjualan." AND id_barang=".$current->id_barang);	
		}

	}
	
	public function delete_detail_penjualan($primary_key)
	{
		$current = $this->db->where('id_detail_penjualan', $primary_key)->where('deleted', 0)->from('tb_detail_penjualan')->get()->row();
		if(count($current) < 1) return false;
		
		$id_penjualan = $current->id_penjualan;
		
		$delete_result = $this->db->update('tb_detail_penjualan', array('deleted'=>'1'),array('id_detail_penjualan'=>$primary_key));
		
		if($delete_result != false){
			//GENERATE NEW TOTAL
			$new_total = $this->db->query('SELECT SUM(subtotal) as total FROM tb_detail_penjualan WHERE deleted = 0 AND id_penjualan='.$id_penjualan)->result();
	
			foreach ($new_total as $data) {
				$total = $data->total;
			}
			$this->db->update('tb_penjualan',array('total' => $total),array('id_penjualan' => $id_penjualan));
	
			//REVERSE ARUS STOK
			$this->db->query("UPDATE tb_barang SET stok = stok + ".$current->jumlah." WHERE id_barang=".$current->id_barang);	
				
			$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok + ".$current->jumlah." WHERE id_penjualan = ".$current->id_penjualan." AND id_barang=".$current->id_barang);	
			
		}
		
		return $delete_result;
	}

	public function total_retur_penjualan($value,$row)
	{
		$query = $this->db->select('tb_penjualan.*,SUM(tb_retur_penjualan.total_retur) as total_retur_penjualan',FALSE)
						  ->from('tb_penjualan')
						  ->join('tb_retur_penjualan','tb_retur_penjualan.id_penjualan=tb_penjualan.id_penjualan','left')
						  ->where('tb_penjualan.id_penjualan',$row->id_penjualan)
						  ->get()
						  ->row();

		return 'Rp.'.number_format($query->total_retur_penjualan);
	}

	public function to_penjualan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Tambah Penjualan';
			$data['pelanggan'] = $this->Transaction_model->get_all_pelanggan();
			$data['sales'] = $this->Transaction_model->get_all_sales();
			$data['payment'] = $this->Transaction_model->get_all_payment();
			$this->load->view('transaction/penjualan', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function detail_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Detail Penjualan';
			$data['pelanggan'] = $this->Transaction_model->get_all_pelanggan();
			$data['sales'] = $this->Transaction_model->get_all_sales();
			$data['payment'] = $this->Transaction_model->get_all_payment();
			$data['detail'] = $this->Transaction_model->detail_penjualan($id_penjualan);
			$this->load->view('report/detail_penjualan', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function to_tambah_detail_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in')){
			$detail = $this->db->query('SELECT * FROM tb_penjualan WHERE id_penjualan='.$id_penjualan)->row();
			$data['title'] = 'Tambah detail Penjualan '.$detail->nomor_penjualan;
			$data['detail'] = $detail;
			$this->load->view('report/tambah_detail_penjualan', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function tindakan_penjualan($value,$row)
	{
		return '<a class="btn btn-primary btn-block" href="'.site_url('admin/to_retur_penjualan/'.$row->id_penjualan).'"><i class="fa fa-plus"></i> Retur Penjualan</a>
			<a class="btn btn-default btn-block" href="'.site_url('admin/edit_detail_penjualan/'.$row->id_penjualan).'"><i class="fa fa-pencil"></i> Edit detail</a>';
	}

	public function to_retur_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Tambah Retur Penjualan';
			$data['retur'] = $this->Transaction_model->retur_penjualan($id_penjualan);
			$data['retur_items'] = $this->Transaction_model->retured_items($id_penjualan);
			$this->load->view('transaction/retur_penjualan', $data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function detail_retur_penjualan($id_retur_penjualan)
   	{
   		if($this->session->userdata('logged_in') == TRUE){
   			$data['retur'] = $this->Transaction_model->retur_penjualan_print($id_retur_penjualan);
   			$data['title'] = 'Detail Retur Penjualan';
   			$this->load->view('report/detail_retur_penjualan', $data, FALSE);
   		}else{
   			redirect('admin','refresh');
   		}
   	}

	public function to_update_retur_penjualan($id_retur_penjualan)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['update'] = $this->Transaction_model->update_retured_items($id_retur_penjualan);
			$data['title'] = 'Update Retur Penjualan';
			$this->load->view('transaction/update_retur_penjualan', $data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function pembelian()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Pembelian');
			$c->set_table('tb_pembelian');
			// if(current_url().'/'.$this->uri->segment(3)){
			// 	$c->where('tb_pembelian.id_supplier',$this->uri->segment(3));
			// 	$c->where('tb_pembelian.deleted',0);
			// }
			$c->where('tb_pembelian.deleted',0);
			$c->unset_edit();
			$c->order_by('id_pembelian','DESC');
			$c->columns('nomor_pembelian','tanggal_pembelian','id_supplier','id_payment','ppn','total','total_retur','status','tindakan');
			$c->display_as('nomor_pembelian','Nomor')
			  ->display_as('tanggal_pembelian','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_supplier','Supplier')
			  ->display_as('ppn','PPN')
			  ->display_as('total_retur','Total Retur');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->unset_read();
			$c->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_supplier','tb_supplier','nama_supplier');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_pembelian'))
		      ->callback_column('ppn',array($this,'ppn_tag'))
		      ->callback_column('total_retur',array($this,'total_retur_pembelian'))
		      ->callback_column('status',array($this,'status_tag'));
		    $c->add_action('Detail', '', 'admin/detail_pembelian', 'fa-file');
		    $c->add_action('Cetak Nota', '', 'admin/print_detail_pembelian', 'fa-print');
		    $c->add_action('Lihat Nota', '', 'admin/cetak_detail_pembelian', 'fa-print');
		    $c->callback_delete(array($this,'delete_pembelian'));
		    $title = 'Data Pembelian';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_pembelian', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_pembelian($primary_key)
	{
		$currentnota = $this->db->where('id_pembelian', $primary_key)->where('deleted', 0)->from('tb_pembelian')->get()->row();
		if(count($currentnota) < 1) return false;
		
		$id_pembelian = $currentnota->id_pembelian;
		
		$delete_result = $this->db->update('tb_pembelian', array('deleted'=>'1'),array('id_pembelian'=>$primary_key));
		
		if($delete_result != false){
			$get_detail_nota = $this->db->query("select * from tb_detail_pembelian where deleted = 0 and id_pembelian = $id_pembelian")->result();
			
			foreach($get_detail_nota as $detailnota){
				//REVERSE ARUS STOK
				$this->db->query("UPDATE tb_barang SET stok = stok - ".$detailnota->jumlah." WHERE id_barang=".$detailnota->id_barang);	
					
				//$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok - ".$detailnota->jumlah." WHERE id_pembelian = ".$id_pembelian." AND id_barang=".$detailnota->id_barang);	
				$this->db->query("UPDATE tb_history_stok SET deleted = 1 WHERE id_pembelian = ".$id_pembelian." AND id_barang=".$detailnota->id_barang);	
				//decided to just delete the history rather than modifying the mod_stok
			}	
			
		}
		
		return $delete_result;
	}

	public function pembelian_by_supplier()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Pembelian');
			$c->set_table('tb_pembelian');
			$c->where('tb_pembelian.id_supplier',$this->uri->segment(3));
			$c->where('tb_pembelian.deleted',0);
			$c->unset_edit();
			$c->order_by('id_pembelian','DESC');
			$c->columns('nomor_pembelian','tanggal_pembelian','id_supplier','id_payment','ppn','total','total_retur','status','tindakan');
			$c->display_as('nomor_pembelian','Nomor')
			  ->display_as('tanggal_pembelian','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_supplier','Supplier')
			  ->display_as('ppn','PPN')
			  ->display_as('total_retur','Total Retur');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->unset_read();
			$c->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_supplier','tb_supplier','nama_supplier');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_pembelian'))
		      ->callback_column('ppn',array($this,'ppn_tag'))
		      ->callback_column('total_retur',array($this,'total_retur_pembelian'))
		      ->callback_column('status',array($this,'status_tag'));
		    $c->add_action('Detail', '', 'admin/detail_pembelian', 'fa-file');
		    $c->add_action('Cetak Nota', '', 'admin/print_detail_pembelian', 'fa-print');
		    $c->add_action('Lihat Nota', '', 'admin/cetak_detail_pembelian', 'fa-print');
		    $c->callback_delete(array($this,'delete_pembelian'));
		    $title = 'Data Pembelian';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_pembelian', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function edit_detail_pembelian()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Detail Pembelian');
			$c->set_table('tb_detail_pembelian');
			$c->where('tb_detail_pembelian.deleted',0);
			$c->where('tb_detail_pembelian.id_pembelian',$this->uri->segment(3));
			$c->order_by('id_detail_pembelian','DESC');
			// $c->columns('id_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','total','total_retur','status','tindakan');
			$c->display_as('id_pembelian','Pembelian')
			  ->display_as('sku_barang','SKU')
			  ->display_as('nama_barang','Barang')
			  ->display_as('diskon', 'Diskon (%)');;
			$c->field_type('id_pembelian', 'readonly')
				->field_type('sku_barang', 'readonly')
				->field_type('nama_barang', 'readonly');  
			$c->unset_columns('id_barang','created','updated','deleted');
			$c->unset_fields('id_barang', 'deleted','created','updated', 'subtotal');
			$c->unset_add();
			$c->unset_read();
			$c->set_relation('id_pembelian','tb_pembelian','nomor_pembelian');
			$c->callback_column('harga',array($this,'format'))
			  ->callback_column('subtotal',array($this,'format'));
		    $c->callback_update(array($this,'update_detail_pembelian'));
		    $c->callback_delete(array($this,'delete_detail_pembelian'));
		    $title = 'Data Detail Pembelian';
		 	$this->load->vars(array('title' => $title, 'state' => $c->getState()));

			$output = $c->render();
			$this->load->view('data_detail_pembelian', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function update_detail_pembelian($post_array,$primary_key)
	{
		$harga = $post_array['harga'];
		$diskon = $post_array['diskon'];
		$qty = $post_array['jumlah'];
		//$id_pembelian = $post_array['id_pembelian'];
		$subtotal = ($harga - ($harga * ($diskon / 100))) * $qty;

		$current = $this->db->where('id_detail_pembelian', $primary_key)->where('deleted', 0)->from('tb_detail_pembelian')->get()->row();
		if(count($current) < 1) return false;

		$id_pembelian = $current->id_pembelian;

		$data = array(
			'harga' => $harga,
			'diskon' => $diskon,
			'jumlah' => $qty,
			'subtotal' => $subtotal
		);

		$this->db->update('tb_detail_pembelian',$data,array('id_detail_pembelian'=>$primary_key));
		
		//GENERATE NEW TOTAL
		$new_total = $this->db->query('SELECT SUM(subtotal) as total FROM tb_detail_pembelian WHERE deleted = 0 AND id_pembelian='.$id_pembelian)->result();

		foreach ($new_total as $data) {
			$total = $data->total;
		}
		$this->db->update('tb_pembelian',array('total' => $total),array('id_pembelian' => $id_pembelian));

		//REVERSE ARUS STOK
		if($current->jumlah != $qty){
			$delta = $qty - $current->jumlah;
			$this->db->query("UPDATE tb_barang SET stok = stok + $delta WHERE id_barang=".$current->id_barang);	
			
			$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok + $delta WHERE id_pembelian = ".$current->id_pembelian." AND id_barang=".$current->id_barang);	
		}

	}
	
	public function delete_detail_pembelian($primary_key)
	{
		$current = $this->db->where('id_detail_pembelian', $primary_key)->where('deleted', 0)->from('tb_detail_pembelian')->get()->row();
		if(count($current) < 1) return false;
		
		$id_pembelian = $current->id_pembelian;
		
		$delete_result = $this->db->update('tb_detail_pembelian', array('deleted'=>'1'),array('id_detail_pembelian'=>$primary_key));
		
		if($delete_result != false){
			//GENERATE NEW TOTAL
			$new_total = $this->db->query('SELECT SUM(subtotal) as total FROM tb_detail_pembelian WHERE deleted = 0 AND id_pembelian='.$id_pembelian)->result();
	
			foreach ($new_total as $data) {
				$total = $data->total;
			}
			$this->db->update('tb_pembelian',array('total' => $total),array('id_pembelian' => $id_pembelian));
	
			//REVERSE ARUS STOK
			$this->db->query("UPDATE tb_barang SET stok = stok - ".$current->jumlah." WHERE id_barang=".$current->id_barang);	
				
			$this->db->query("UPDATE tb_history_stok SET mod_stok = mod_stok - ".$current->jumlah." WHERE id_pembelian = ".$current->id_pembelian." AND id_barang=".$current->id_barang);	
			
		}
		
		return $delete_result;
	}

	public function total_retur_pembelian($value,$row)
	{
		$query = $this->db->select('tb_pembelian.*,SUM(tb_retur_pembelian.total_retur_pembelian) as total_retur_pembelian',FALSE)
						  ->from('tb_pembelian')
						  ->join('tb_retur_pembelian','tb_retur_pembelian.id_pembelian=tb_pembelian.id_pembelian','left')
						  ->where('tb_pembelian.id_pembelian',$row->id_pembelian)
						  ->get()
						  ->row();

		return 'Rp.'.number_format($query->total_retur_pembelian);
	}

	public function to_retur_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Add Retur Pembelian';
			$data['retur'] = $this->Transaction_model->retur_pembelian($id_pembelian);
			$data['retured_items'] = $this->Transaction_model->retured_items_beli($id_pembelian);
			$this->load->view('transaction/retur_pembelian',$data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function detail_retur_pembelian($id_retur_pembelian)
   	{
   		if($this->session->userdata('logged_in') == TRUE){
   			$data['retur'] = $this->Transaction_model->retur_pembelian_print($id_retur_pembelian);
   			$data['title'] = 'Detail Retur Pembelian';
   			$this->load->view('report/detail_retur_pembelian', $data, FALSE);
   		}else{
   			redirect('admin','refresh');
   		}
   	}

	public function to_update_retur_pembelian($id_retur_pembelian)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Update Retur Pembelian';
			$data['update'] = $this->Transaction_model->update_retured_items_beli($id_retur_pembelian);
			$this->load->view('transaction/update_retur_pembelian', $data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function detail_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Detail Pembelian';
			$data['supplier'] = $this->Transaction_model->get_all_supplier();
			$data['payment'] = $this->Transaction_model->get_all_payment();
			$data['detail'] = $this->Transaction_model->detail_pembelian($id_pembelian);
			$this->load->view('report/detail_pembelian', $data, FALSE);
		}else{
			redirect('admin','refresh');
		}
	}

	public function tindakan_pembelian($value,$row)
	{
		return '<a class="btn btn-primary btn-block" href="'.site_url('admin/to_retur_pembelian/'.$row->id_pembelian).'"><i class="fa fa-plus"></i> Retur Pembelian</a>
				<a class="btn btn-default btn-block" href="'.site_url('admin/edit_detail_pembelian/'.$row->id_pembelian).'"><i class="fa fa-pencil"></i> Edit detail</a>';
	}

	public function to_pembelian()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Tambah Pembelian';
			$data['supplier'] = $this->Transaction_model->get_all_supplier();
			$data['payment'] = $this->Transaction_model->get_all_payment();
			$this->load->view('transaction/pembelian', $data);
		}else{
			redirect('admin');
		}

	}

	public function to_tambah_detail_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in')){
			$detail = $this->db->query('SELECT * FROM tb_pembelian WHERE id_pembelian='.$id_pembelian)->row();
			$data['title'] = 'Tambah detail pembelian '.$detail->nomor_pembelian;
			$data['detail'] = $detail;
			$this->load->view('report/tambah_detail_pembelian', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function to_barang()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Tambah / Update Barang';
			$this->load->view('add_barang', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function format($value)
	{
		return 'Rp.'.number_format($value);
	}

	public function ppn_tag($value,$row)
	{
		if($row->ppn == 'Include'){
			return '<div class="label label-success">'.$value.'</div>';
		}else{
			return '<div class="label label-danger">'.$value.'</div>';
		}
	}

	public function get_scan_barang()
	{
		if ($this->session->userdata('logged_in') == TRUE) {
			$scan_data = $this->input->post('search_data');

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
		
	public function get_scan_barang_beli()
	{
		if ($this->session->userdata('logged_in') == TRUE) {
			$scan_data = $this->input->post('search_data');

			$result = $this->Transaction_model->get_scan_barang($scan_data);

	        if (!empty($result))
	          	{
	               foreach ($result as $row){
	               	if($row->stok > 0 || 1 == 1){ //no need pengecekan stock
	               		if($row->deleted == 0){
										 echo '
										 <li>
		               			<a class="list" style="display:block;cursor:pointer" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_modal.'" onclick="add_barang(this);">
					                <div class="row">
						                <div class="col-sm-6">
						               ' . $row->nama_barang . '
						                </div>
						                <div class="col-sm-6">
					                		<button type="button" class="add_cart btn btn-success btn-sm" data-produk-id="'.$row->id_barang.'" data-produkkode="'.$row->sku_barang.'" data-produknama="'.$row->nama_barang.'" data-produkharga="'.$row->harga_modal.'" data-jual="'.base_url().'index.php/admin/to_penjualan" data-beli="'.base_url().'index.php/admin/to_pembelian" onclick="add_barang(this);"><i class="fa fa-plus"></i></button>
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

	public function scan_barang()
	{
		if ($this->session->userdata('logged_in') == TRUE) {
			$num=(!empty($_POST["search_data"]))?$_POST["search_data"]:die('Search is empty');

			$scan_data =  $this->input->post('search_data');

			$query = $this->Transaction_model->get_scan_barang($scan_data);

				if ($num == $scan_data )
				{
					foreach ($query as $value) {
						$fill = array(
							'id_barang' => $value->id_barang,
						   	'sku_barang' => $value->sku_barang,
						    'nama_barang' => $value->nama_barang,
						    'harga_jual' => $value->harga_jual,
						    'harga_modal' => $value->harga_modal,
						    'qty' => 1,
						    );
						}
						   echo json_encode($fill);
				}else{
					echo "Bad input.";
			}
		}else{
			redirect('admin','refresh');
		}

	}
	
	public function scan_barang_beli()
	{
		if ($this->session->userdata('logged_in') == TRUE) {
			$num=(!empty($_POST["search_data"]))?$_POST["search_data"]:die('Search is empty');

			$scan_data =  $this->input->post('search_data');

			$query = $this->Transaction_model->get_scan_barang($scan_data);

				if ($num == $scan_data )
				{
					foreach ($query as $value) {
						$fill = array(
							'id_barang' => $value->id_barang,
						   	'sku_barang' => $value->sku_barang,
						    'nama_barang' => $value->nama_barang,
						    'harga_jual' => $value->harga_jual,
						    'harga_modal' => $value->harga_modal,
						    'qty' => 1,
						    );
						}
						   echo json_encode($fill);
				}else{
					echo "Bad input.";
			}
		}else{
			redirect('admin','refresh');
		}

	}

	public function simpan_penjualan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data = $this->Transaction_model->simpan_penjualan();
			//echo json_encode($data);
			echo $data;
		}else{
			redirect('admin','refresh');
		}

	}

	public function simpan_pembelian()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data = $this->Transaction_model->simpan_pembelian();
			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}

	}

	public function history_stok()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('History Stok');
			$c->set_table('tb_history_stok');
			$c->where('tb_history_stok.deleted',0);
			$c->order_by('tb_history_stok.created','DESC');
			$c->unset_columns('created','updated','deleted');
			$c->unset_fields('created','updated','deleted');
			$c->unset_add();
			$c->unset_edit();
			$c->display_as('id_barang','Nama Barang')
				->display_as('id_penjualan','Penjualan')
				->display_as('id_pembelian','Pembelian');
			$c->set_relation('id_barang','tb_barang','nama_barang');
			$c->set_relation('id_penjualan','tb_penjualan','{nomor_penjualan}');
			$c->set_relation('id_pembelian','tb_pembelian','{nomor_pembelian}');
			$c->callback_column($this->unique_field_name('id_pembelian'), array($this, 'link_pembelian'));
			$c->callback_column($this->unique_field_name('id_penjualan'), array($this, 'link_penjualan'));
			$title = 'Data History Stok';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_history_stok', $output);
		}else{
			redirect('admin','refresh');
		}
	}
	
	function unique_field_name($field_name) {
	    return 's'.substr(md5($field_name),0,8); //This s is because is better for a string to begin with a letter and not with a number
    }
	
	public function link_pembelian($value, $row){
		return "<a href='detail_pembelian/".$row->id_pembelian."' target='_blank'>$value</a>";
	}
	
	public function link_penjualan($value, $row){
		return "<a href='detail_penjualan/".$row->id_penjualan."' target='_blank'>$value</a>";
	}

	public function history_harga()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('History Harga');
			$c->set_table('tb_history_harga_barang');
			$c->where('tb_history_harga_barang.deleted',0);
			$c->order_by('tb_history_harga_barang.created','DESC');
			$c->unset_columns('created','updated','deleted');
			$c->unset_fields('created','updated','deleted');
			$c->unset_add();
			$c->unset_edit();
			$c->display_as('id_barang','Nama Barang');
			$c->set_relation('id_barang','tb_barang','nama_barang');
			$title = 'Data History Harga';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_history_harga', $output);
		}else{
			redirect('admin','refresh');
		}		
	}

	public function retur_penjualan_print($id_retur_penjualan)
	{
			$data['cetak_retur'] = $this->Transaction_model->retur_penjualan_print($id_retur_penjualan);
			$this->load->view('transaction/cetak_retur_penjualan',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Retur Penjualan - '.date('hi').'.pdf',array('Attachment'=>0));
	}

	public function retur_pembelian_print($id_retur_pembelian)
	{
			$data['cetak_retur'] = $this->Transaction_model->retur_pembelian_print($id_retur_pembelian);
			$this->load->view('transaction/cetak_retur_pembelian',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Retur Pembelian - '.date('hi').'.pdf',array('Attachment'=>0));
	}

	public function data_supplier_progress()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Data Pembelian /Supplier';
			$data['supplier'] = $this->Transaction_model->get_supplier_progress_data();
			$this->load->view('report/data_supplier_progress', $data);
		}else{
			redirect('admin','refresh');
		}
	}

    public function penjualan_by_sales()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Penjualan');
			$c->set_table('tb_penjualan');
			$c->where('tb_penjualan.deleted',0);
			if($this->uri->segment(3) != NULL){
				$c->where('tb_penjualan.id_sales',$this->uri->segment(3));
				$c->where('tb_penjualan.deleted',0);
			}else{
				$c->where('tb_penjualan.deleted',0);
			}
			$c->where('tb_penjualan.deleted',0);
			$c->order_by('id_penjualan','DESC');
			$c->columns('nomor_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','bayar','sisa','total','tindakan');
			$c->display_as('nomor_penjualan','Nomor')
			  ->display_as('tanggal_penjualan','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_pelanggan','Pelanggan')
			  ->display_as('id_sales','Sales');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->unset_edit();
			$c->unset_read();
			$c->set_relation('id_sales','tb_sales','nama_sales')
			  ->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_pelanggan','tb_pelanggan','nama_pelanggan');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_penjualan'));

		    $title = 'Data Penjualan by Sales';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_penjualan', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function data_sales_progress()
	 {
	 	if($this->session->userdata('logged_in') == TRUE){
		 	$data['title'] = 'Data Omset Penjualan /Sales';
		 	$data['sales'] = $this->Transaction_model->get_sales_progress_data();
		 	$this->load->view('report/data_sales_progress', $data);
		 }else{
		 	redirect('admin','refresh');
		 }
	 }

	 public function penjualan_by_pelanggan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Penjualan');
			$c->set_table('tb_penjualan');
			$c->where('tb_penjualan.deleted',0);
			if($this->uri->segment(3) != NULL){
				$c->where('tb_penjualan.id_pelanggan',$this->uri->segment(3));
			}else{
				$c->where('tb_penjualan.deleted',0);
			}
			$c->order_by('id_penjualan','DESC');
			$c->columns('nomor_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','bayar','sisa','total','tindakan');
			$c->display_as('nomor_penjualan','Nomor')
			  ->display_as('tanggal_penjualan','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_pelanggan','Pelanggan')
			  ->display_as('id_sales','Sales');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->unset_edit();
			$c->unset_read();
			$c->set_relation('id_sales','tb_sales','nama_sales')
			  ->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_pelanggan','tb_pelanggan','nama_pelanggan');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_penjualan'));

		    $title = 'Data Penjualan by Pelanggan';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_penjualan', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	 public function data_pelanggan_progress()
	 {
	 	if($this->session->userdata('logged_in') == TRUE){
		 	$data['title'] = 'Data Omset Penjualan /Pelanggan';
		 	$data['pelanggan'] = $this->Transaction_model->get_pelanggan_progress_data();
		 	$this->load->view('report/data_pelanggan_progress', $data);
		 }else{
		 	redirect('admin','refresh');
		 }
	 }

	public function laporan_penjualan()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Laporan Penjualan';
			$this->load->view('report/laporan_penjualan', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function cari_laporan()
    {
	    //date_default_timezone_set('Asia/Jakarta');
    	if($this->session->userdata('logged_in') == TRUE){

		    if(isset($_POST["from_date"], $_POST["to_date"]))
		 	{
		      //$connect = mysqli_connect("localhost", "root", "", "software_toko_variasi");
		      $output = '';
		      $total = 0;
		      $no = 0;
		      $fromdate = date("Y-m-d 00:00:00", strtotime($_POST["to_date"]));
		      $todate = date("Y-m-d 23:59:59", strtotime($_POST["to_date"]." +1 day"));
		      $query = "
		           SELECT * FROM tb_penjualan INNER JOIN tb_pelanggan ON tb_penjualan.id_pelanggan = tb_pelanggan.id_pelanggan WHERE tanggal_penjualan >= '$fromdate' AND tanggal_penjualan <= '$todate' ORDER BY tanggal_penjualan ASC";
		           
		      //$result = mysqli_query($connect, $query);
		      $result = $this->db->query($query)->result();
		      $output .= '
		      	<br>
		      	<div style="width:100%">
		      		<h3 style="text-align:center">LAPORAN PENJUALAN</h3>
		      		<h4 style="text-align:center">Periode</h4>
		      		<h4 style="text-align:center">'.date('d F Y',strtotime($_POST["from_date"])).' s/d. '.date('d F Y',strtotime($_POST["to_date"])).'</h4>
		      	</div>
		      	<br>
		      	<table class="table table-bordered" border="1" cellpadding="3" style="width:100%;border-collapse:collapse" id="mytable">
		      	<thead>
		           <tr>
		            <th>No</th>
		            <th>Nomor Penjualan</th>
		            <th>Tanggal Penjualan</th>
		            <th>Pelanggan</th>
		            <th>Total</th>
		           </tr>
		         </thead>
		      ';
		      if(count($result) > 0)
		      {
		           foreach($result as $row)
		           {
		                $output .= '
		                	<tbody id="all">
		                     <tr>
		                          <td>'.++$no.'</td>
		                          <td><a href="detail_penjualan/'.$row->id_penjualan.'" target="_blank">'.$row->nomor_penjualan.'</a></td>
		                          <td>'.date('d F Y',strtotime($row->tanggal_penjualan)).'</td>
		                          <td>'.$row->nama_pelanggan .'</td>
		                          <td>Rp '.number_format($row->total).'</td>
		                     </tr>
		                ';
		                $total+= $row->total;
		           }

		           $output .= '
		           <tr>
		              <td colspan="4" style="text-align: right;padding:5px"><b> Grand Total</b></td>
		               <td>Rp '.number_format($total).'</td>
		            </tr>
		            ';
		      }
		      else
		      {
		           $output .= '
		                <tr>
		                     <td colspan="5"><i class="fa fa-times" style="color:red"></i> Penjualan tidak ditemukan</td>
		                </tr>
		           ';
		      }
		      $output .= '
		      </tbody>
		      </table>';
		      echo $output;
		 	}
		}else{
			redirect('admin','refresh');
		}

   	}

   	public function cetak_penjualan_by_sales($id_sales)
	{
			$data['cetak'] = $this->Transaction_model->cetak_penjualan_by_sales($id_sales);
			$this->load->view('report/cetak_penjualan_by_sales',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Laporan by Sales - '.date('hi').'.pdf',array('Attachment'=>0));
	}

	public function cetak_pembelian_by_supplier($id_supplier)
	{
			$data['cetak'] = $this->Transaction_model->cetak_pembelian_by_supplier($id_supplier);
			$this->load->view('report/cetak_pembelian_by_supplier',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Laporan by Sales - '.date('hi').'.pdf',array('Attachment'=>0));
	}

	public function cetak_penjualan_by_pelanggan($id_pelanggan)
	{
			$data['cetak'] = $this->Transaction_model->cetak_penjualan_by_pelanggan($id_pelanggan);
			$this->load->view('report/cetak_penjualan_by_pelanggan',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Laporan by Sales - '.date('hi').'.pdf',array('Attachment'=>0));
	}

	public function kartu_stok($id_barang)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data['title'] = 'Kartu Stok';
			$data['kartu_stok'] = $this->Transaction_model->kartu_stok($id_barang);
			$this->load->view('report/kartu_stok', $data, FALSE);
		}else{
			redirect('admin','refresh');
		}
	}

	public function detail_harga_penjualan($id_barang, $id_pelanggan = 0)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data = $this->Transaction_model->detail_harga_penjualan($id_barang, $id_pelanggan);

			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function detail_harga_pembelian($id_barang, $id_supplier = 0)
	{
		if($this->session->userdata('logged_in') == TRUE){
			$data = $this->Transaction_model->detail_harga_pembelian($id_barang, $id_supplier);

			echo json_encode($data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function payment()
	{
		if($this->session->userdata('logged_in')){
			$c = new grocery_CRUD();

			$c->set_subject('Payment');
			$c->set_table('tb_master_payment');
			$c->unset_columns('created','updated','deleted');
			$title = 'Data Payment';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_payment', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function cetak_detail_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in')){
			$jual = $this->Transaction_model->cetak_detail_penjualan($id_penjualan);
			$nomor_penjualan = $jual->nomor_penjualan;
			$data['detail'] = $this->Transaction_model->cetak_detail_penjualan($id_penjualan);
			$this->load->view('report/cetak_detail_penjualan',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5PmarserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Penjualan - '.$nomor_penjualan.'.pdf',array('Attachment'=>0));
		}else{
			redirect('admin','refresh');
		}
	}

	public function print_detail_penjualan($id_penjualan)
	{
		if($this->session->userdata('logged_in')){
			$jual = $this->Transaction_model->cetak_detail_penjualan($id_penjualan);
			$nomor_penjualan = $jual->nomor_penjualan;
			$data['detail'] = $this->Transaction_model->cetak_detail_penjualan($id_penjualan);
			
			$cupsdet = $data['detail'];
			//print_r($cupsdet);
			//exit();
			
			try {
				date_default_timezone_set('Asia/Jakarta');
				
                $this->load->library('EscPos.php');
                
                $connector = new Escpos\PrintConnectors\CupsPrintConnector('EPSON_LX-300');
                //$receiptprint->connect('192.168.15.200', 9100);
                $printer = new Escpos\Printer($connector);
                
                
                
				//$date = date("d M Y H:i:s", strtotime($cupsdet->tanggal_invoice));
				
				$nama = substr($cupsdet->nama_pelanggan, 0, 38);
				$alamat = substr($cupsdet->alamat_pelanggan, 0, 38);
				$alamat2 = "";
				if(strlen($cuspdet->alamat_pelanggan) > 38) $alamat2 = substr($cupsdet->alamat_pelanggan, 38, 38);
				$kota = substr($cupsdet->kota_pelanggan, 0, 38);
				$telp = substr($cupsdet->nomor_telepon_pelanggan, 0, 38);
				
				$sales = substr($cupsdet->nama_sales, 0, 38); 
				
				$lbltgl = "";
				$showtgl = "";
				if($cupsdet->status == "Belum Terbayar"){
					$lbltgl = "Jatuh Tempo: ";
					$showtgl = date("d M Y", strtotime($cupsdet->tanggal_jatuh_tempo));
				}
				else if($cupsdet->status == "Lunas"){
					$lbltgl = "Tanggal Lunas: ";
					$showtgl = date("d M Y", strtotime($cupsdet->tanggal_lunas));
				}
				
				//$printer->text("123456789a123456789b123456789c123456789d123456789e123456789f123456789g123456789h"); // BATAS KANAN
				$printer -> selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);
				$printer -> setJustification(Escpos\Printer::JUSTIFY_CENTER);
				$printer -> text("INLIN JAYA VARIASI\n");
				$printer -> selectPrintMode();
				$printer -> text("FAKTUR PENJUALAN");
				$printer -> text("\n");
				$printer -> setJustification(Escpos\Printer::JUSTIFY_LEFT);
				$printer -> text(new leftright("No Faktur: ".$cupsdet->nomor_penjualan,"Kepada Yth."));
				$printer -> text(new leftright("Tgl. Faktur: ".date("d M Y", strtotime($cupsdet->tanggal_penjualan)), $nama)); //line7
				$printer -> text(new leftright("Status: ".$cupsdet->status, $alamat)); //line7
				$printer -> text(new leftright("Payment: ".$cupsdet->nama_payment, $alamat2)); //line7
				$printer -> text(new leftright($lbltgl.$showtgl, $kota)); //line7
				$printer -> text(new leftright("Sales: ".$sales, $telp)); //line7
				
				//$printer->text("\n");
				//$printer->text("123456789a123456789b123456789c123456789d123456789e123456789f123456789g123456789h"); // BATAS KANAN
				$printer -> text(" ------------------------------------------------------------------------------ \n");
				$printer -> text("|NO|NAMA BARANG                 |   QTY   |     HARGA    | DISC |   SUBTOTAL   |\n");
				$printer -> text(" ------------------------------------------------------------------------------ \n");
				
				$itemnum = 1;
				$linenum = 1;
				
				$cetak = $this->db->query('SELECT * FROM tb_detail_penjualan WHERE deleted = 0 and id_penjualan='.$cupsdet->id_penjualan)->result();
				foreach ($cetak as $data) {
					if($data->subtotal != 0){
						
						$isatuan = "";
						
						$satuan = $this->db->query('SELECT satuan FROM tb_barang WHERE id_barang='.$data->id_barang)->result();
						if(count($satuan) > 0)
							$isatuan = $satuan[0]->satuan;
						
						$inama = $data->nama_barang;
						$inama2 = "";
						$iqty = $data->jumlah." ".$isatuan;
						$iqty2 = "";
						$iharga = "Rp".number_format($data->harga);
						$idiskon = $data->diskon."%";
						$isubtotal = "Rp".number_format($data->subtotal);
						
						$lineadded = false;
						
						if(strlen($inama) > 26){
							$inama2 = substr($inama, 26, 26);
							$inama = substr($inama, 0, 26);
							
							if(!$lineadded){
								$lineadded = true;
								$linenum++;
							}
						}
						
						if(strlen($iqty) > 8){
							$iqty2 = substr($iqty, 8, 8);
							$iqty = substr($iqty, 0, 8);
							
							if(!$lineadded){
								$lineadded = true;
								$linenum++;
							}
						}
						
						$printer->text(new item($itemnum,$inama,$iqty,$iharga,$idiskon,$isubtotal));
						if($lineadded)
							$printer->text(new item("",$inama2,$iqty2,"","",""));
						
						$linenum++;
						$itemnum++;
					}	
				}
				
				while($linenum <= 11){
					$printer->text(new item("","","","","",""));
					$linenum++;
				}
				
				$printer -> text(" ------------------------------------------------------------------------------");
				$printer -> setJustification(Escpos\Printer::JUSTIFY_RIGHT);
				$printer -> selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);
				$printer -> text("TOTAL: Rp".number_format($cupsdet->total));
				$printer -> selectPrintMode();
				$printer -> setJustification(Escpos\Printer::JUSTIFY_LEFT);
				$printer -> text(" ------------------------------------------------------------------------------");
				
				$printer -> setJustification(Escpos\Printer::JUSTIFY_RIGHT);
				$printer -> text("Pembeli             Penjual               Mengetahui   \n");
				$printer->text("\n\n\n\n\n\n");
				$printer->text(" ");

				$printer -> close();
				
            } catch (Exception $e) {
                log_message("error", "Error: Could not print. Message ".$e->getMessage());
                echo "test".$e->getMessage(); exit();
                //$printer->close_after_exception();
            }
			
			redirect('admin/penjualan');
		}else{
			redirect('admin','refresh');
		}
	}

	public function cetak_detail_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in')){
			$jual = $this->Transaction_model->cetak_detail_pembelian($id_pembelian);
			$nomor_pembelian = $jual->nomor_pembelian;
			$data['detail'] = $this->Transaction_model->cetak_detail_pembelian($id_pembelian);
			$this->load->view('report/cetak_detail_pembelian',$data);
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$this->dompdf->setPaper('A4','portrait');
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->loadHtml($html);
			$this->dompdf->render();
			$this->dompdf->stream('Pembelian - '.$nomor_pembelian.'.pdf',array('Attachment'=>0));
		}else{
			redirect('admin','refresh');
		}
	}

	public function print_detail_pembelian($id_pembelian)
	{
		if($this->session->userdata('logged_in')){
			$jual = $this->Transaction_model->cetak_detail_pembelian($id_pembelian);
			$nomor_pembelian = $jual->nomor_pembelian;
			$data['detail'] = $this->Transaction_model->cetak_detail_pembelian($id_pembelian);
			
			$cupsdet = $data['detail'];
			//print_r($cupsdet);
			//exit();
			
			try {
				date_default_timezone_set('Asia/Jakarta');
				
                $this->load->library('EscPos.php');
                
                $connector = new Escpos\PrintConnectors\CupsPrintConnector('EPSON_LX-300');
                //$receiptprint->connect('192.168.15.200', 9100);
                $printer = new Escpos\Printer($connector);
                
				//$date = date("d M Y H:i:s", strtotime($cupsdet->tanggal_invoice));
				
				$nama = substr($cupsdet->nama_supplier, 0, 38);
				$alamat = substr($cupsdet->alamat_supplier, 0, 38);
				$alamat2 = "";
				if(strlen($cuspdet->alamat_supplier) > 38) $alamat2 = substr($cupsdet->alamat_supplier, 38, 38);
				$kota = substr($cupsdet->kota_supplier, 0, 38);
				$telp = substr($cupsdet->nomor_telepon_supplier, 0, 38);
				
				
				$lbltgl = "";
				$showtgl = "";
				if($cupsdet->status == "Belum Terbayar"){
					$lbltgl = "Jatuh Tempo: ";
					$showtgl = date("d M Y", strtotime($cupsdet->tanggal_jatuh_tempo));
				}
				else if($cupsdet->status == "Lunas"){
					$lbltgl = "Tanggal Lunas: ";
					$showtgl = date("d M Y", strtotime($cupsdet->tanggal_lunas));
				}
				
				//$printer->text("123456789a123456789b123456789c123456789d123456789e123456789f123456789g123456789h"); // BATAS KANAN
				$printer -> selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);
				$printer -> setJustification(Escpos\Printer::JUSTIFY_CENTER);
				$printer -> text("INLIN JAYA VARIASI\n");
				$printer -> selectPrintMode();
				$printer -> text("FAKTUR PEMBELIAN");
				$printer -> text("\n");
				$printer -> setJustification(Escpos\Printer::JUSTIFY_LEFT);
				$printer -> text(new leftright("No Faktur: ".$cupsdet->nomor_pembelian,"Kepada Yth."));
				$printer -> text(new leftright("Tgl. Faktur: ".date("d M Y", strtotime($cupsdet->tanggal_pembelian)), $nama)); //line7
				$printer -> text(new leftright("Status: ".$cupsdet->status, $alamat)); //line7
				$printer -> text(new leftright("Payment: ".$cupsdet->nama_payment, $alamat2)); //line7
				$printer -> text(new leftright($lbltgl.$showtgl, $kota)); //line7
				$printer -> text(new leftright("".$sales, $telp)); //line7
				
				//$printer->text("\n");
				//$printer->text("123456789a123456789b123456789c123456789d123456789e123456789f123456789g123456789h"); // BATAS KANAN
				$printer -> text(" ------------------------------------------------------------------------------ \n");
				$printer -> text("|NO|NAMA BARANG                 |   QTY   |     HARGA    | DISC |   SUBTOTAL   |\n");
				$printer -> text(" ------------------------------------------------------------------------------ \n");
				
				$itemnum = 1;
				$linenum = 1;
				
				$cetak = $this->db->query('SELECT * FROM tb_detail_pembelian WHERE deleted = 0 and id_pembelian='.$cupsdet->id_pembelian)->result();
				foreach ($cetak as $data) {
					if($data->subtotal != 0){
						
						$isatuan = "";
						
						$satuan = $this->db->query('SELECT satuan FROM tb_barang WHERE id_barang='.$data->id_barang)->result();
						if(count($satuan) > 0)
							$isatuan = $satuan[0]->satuan;
						
						$inama = $data->nama_barang;
						$inama2 = "";
						$iqty = $data->jumlah." ".$isatuan;
						$iqty2 = "";
						$iharga = "Rp".number_format($data->harga);
						$idiskon = $data->diskon."%";
						$isubtotal = "Rp".number_format($data->subtotal);
						
						$lineadded = false;
						
						if(strlen($inama) > 26){
							$inama2 = substr($inama, 26, 26);
							$inama = substr($inama, 0, 26);
							
							if(!$lineadded){
								$lineadded = true;
								$linenum++;
							}
						}
						
						if(strlen($iqty) > 8){
							$iqty2 = substr($iqty, 8, 8);
							$iqty = substr($iqty, 0, 8);
							
							if(!$lineadded){
								$lineadded = true;
								$linenum++;
							}
						}
						
						$printer->text(new item($itemnum,$inama,$iqty,$iharga,$idiskon,$isubtotal));
						if($lineadded)
							$printer->text(new item("",$inama2,$iqty2,"","",""));
						
						$linenum++;
						$itemnum++;
					}	
				}
				
				while($linenum <= 11){
					$printer->text(new item("","","","","",""));
					$linenum++;
				}
				
				$printer -> text(" ------------------------------------------------------------------------------");
				$printer -> setJustification(Escpos\Printer::JUSTIFY_RIGHT);
				$printer -> selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);
				$printer -> text("TOTAL: Rp".number_format($cupsdet->total));
				$printer -> selectPrintMode();
				$printer -> setJustification(Escpos\Printer::JUSTIFY_LEFT);
				$printer -> text(" ------------------------------------------------------------------------------");
				
				$printer -> setJustification(Escpos\Printer::JUSTIFY_RIGHT);
				$printer -> text("Pembeli             Penjual               Mengetahui   \n");
				$printer->text("\n\n\n\n\n\n");
				$printer->text(" ");
				$printer -> close();
				
            } catch (Exception $e) {
                log_message("error", "Error: Could not print. Message ".$e->getMessage());
                echo "test".$e->getMessage(); exit();
                //$printer->close_after_exception();
            }
			
			redirect('admin/pembelian');
		}else{
			redirect('admin','refresh');
		}
	}

	public function giro_keluar()
	{
		if($this->session->userdata('logged_in')){
			$c = new grocery_CRUD();

			$c->set_subject('Giro Keluar');
			$c->set_table('tb_giro_keluar');
			$c->unset_columns('created','updated','deleted');
			$c->unset_add();
			$c->unset_fields('created','updated','deleted');
			$c->display_as('id_pembelian','Pembelian')
			  ->display_as('no_giro','Nomor')
			  ->display_as('nominal_giro','Nominal')
			  ->display_as('tanggal_jatuh_tempo','Jatuh Tempo')
			  ->display_as('nama_bank','BANK')
			  ->display_as('keterangan_giro','Keterangan');
			$c->set_relation('id_pembelian','tb_pembelian','nomor_pembelian');
			$c->callback_column('nominal_giro',array($this,'format'));
			$c->callback_delete(array($this,'delete_giro_keluar'));
			$title = 'Data Giro Keluar';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_giro_keluar', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_giro_keluar($primary_key)
	{
		return $this->db->update('tb_giro_keluar', array('deleted'=>'1'),array('id_giro_keluar'=>$primary_key));
	}

	public function giro_masuk()
	{
		if($this->session->userdata('logged_in')){
			$c = new grocery_CRUD();

			$c->set_subject('Giro Masuk');
			$c->set_table('tb_giro_masuk');
			$c->unset_columns('created','updated','deleted');
			$c->unset_add();
			$c->set_relation('id_penjualan','tb_penjualan','nomor_penjualan');
			$c->unset_fields('created','updated','deleted');
			$c->display_as('id_penjualan','Penjualan')
			  ->display_as('no_giro','Nomor')
			  ->display_as('nominal_giro','Nominal')
			  ->display_as('tanggal_jatuh_tempo','Jatuh Tempo')
			  ->display_as('nama_bank','BANK')
			  ->display_as('keterangan_giro','Keterangan')
			  ->display_as('status_giro','Status');
			$c->callback_column('nominal_giro',array($this,'format'));
			$c->callback_delete(array($this,'delete_giro_masuk'));
			$title = 'Data Giro Masuk';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('data_giro_masuk', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_giro_masuk($primary_key)
	{
		return $this->db->update('tb_giro_masuk', array('deleted'=>'1'),array('id_giro_masuk'=>$primary_key));
		
	}

	public function piutang()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Data Piutang');
			$c->set_table('tb_penjualan');
			$c->where('tb_penjualan.deleted',0)
			  ->where('tb_penjualan.status','Belum Terbayar');
			$c->order_by('tb_penjualan.tanggal_jatuh_tempo','DESC');
			if($this->uri->segment(3) != NULL){
				$c->where('tb_penjualan.id_sales',$this->uri->segment(3));
			}else{
				$c->where('tb_penjualan.deleted',0);
			}
			$c->unset_edit();
			$c->unset_read();
			$c->columns('nomor_penjualan','tanggal_penjualan','id_pelanggan','id_sales','ppn','total','total_retur','status','tindakan');
			$c->display_as('nomor_penjualan','Nomor')
			  ->display_as('tanggal_penjualan','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_pelanggan','Pelanggan')
			  ->display_as('id_sales','Sales')
			  ->display_as('ppn','PPN')
			  ->display_as('total_retur','Total Retur');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->set_relation('id_sales','tb_sales','nama_sales')
			  ->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_pelanggan','tb_pelanggan','nama_pelanggan');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_penjualan'))
		      ->callback_column('ppn',array($this,'ppn_tag'))
		      ->callback_column('total_retur',array($this,'total_retur_penjualan'))
		      ->callback_column('status',array($this,'status_tag'));
		    $c->add_action('Detail', '', 'admin/detail_penjualan', 'fa-file');
		    $c->add_action('Cetak Nota', '', 'admin/cetak_detail_penjualan', 'fa-print');
		    $title = 'Data Piutang';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('report/data_piutang', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function hutang()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Data Hutang');
			$c->set_table('tb_pembelian');
			$c->where('tb_pembelian.deleted',0)
			  ->where('tb_pembelian.status','Belum Terbayar');
			$c->order_by('tb_pembelian.tanggal_jatuh_tempo','DESC');
			if($this->uri->segment(3) != NULL){
				$c->where('tb_pembelian.id_supplier',$this->uri->segment(3));
			}else{
				$c->where('tb_pembelian.deleted',0);
			}
			$c->unset_edit();
			$c->columns('nomor_pembelian','tanggal_pembelian','id_supplier','id_payment','ppn','total','total_retur','status','tindakan');
			$c->display_as('nomor_pembelian','Nomor')
			  ->display_as('tanggal_pembelian','Tanggal')
			  ->display_as('id_payment','Payment')
			  ->display_as('id_supplier','Supplier')
			  ->display_as('ppn','PPN')
			  ->display_as('total_retur','Total Retur');
			$c->unset_columns('no_serial','created','updated','deleted');
			$c->unset_fields('deleted','created','updated');
			$c->unset_add();
			$c->unset_read();
			$c->set_relation('id_payment','tb_master_payment','nama_payment')
			  ->set_relation('id_supplier','tb_supplier','nama_supplier');
			$c->callback_column('bayar',array($this,'format'))
		      ->callback_column('sisa',array($this,'format'))
		      ->callback_column('total',array($this,'format'))
		      ->callback_column('tindakan',array($this,'tindakan_pembelian'))
		      ->callback_column('ppn',array($this,'ppn_tag'))
		      ->callback_column('total_retur',array($this,'total_retur_pembelian'))
		      ->callback_column('status',array($this,'status_tag'));
		    $c->add_action('Detail', '', 'admin/detail_pembelian', 'fa-file');
		    $c->add_action('Cetak Nota', '', 'admin/cetak_detail_pembelian', 'fa-print');
		    $title = 'Data Hutang';
		 	$this->load->vars(array('title' => $title));

			$output = $c->render();
			$this->load->view('report/data_hutang', $output);
		}else{
			redirect('admin','refresh');
		}
	}

	public function status_tag($value,$row)
	{
		if($row->status == 'Belum Terbayar'){
			return '<div class="label label-danger">'.$value.'</div>';
		}else if($row->status == 'Lunas'){
			return '<div class="label label-success">'.$value.'</div>';
		}else{
			return '<div class="label label-default">'.$value.'</div>';
		}
	}

	public function stock_opname_prep()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Print Table Stock Opname');
			$c->where('tb_barang.deleted',0);
			$c->order_by('nama_barang','ASC');
			$c->set_table('tb_barang');
			$c->unset_add();
			$c->unset_read();
			$c->unset_edit();
			$c->unset_delete();
			$c->display_as('nama_barang','Barang')
			  ->display_as('sku_barang','SKU Barang')
			  ->display_as('stok','Stok Database')
			  ->display_as('satuan','Stok Gudang')
			  ->display_as('created','Catatan');
			$c->unset_columns('sku_barang','id_barang','harga_modal','harga_jual','updated','deleted');
			$c->callback_column('satuan',array($this,'set_kosong'));
			$c->callback_column('created',array($this,'set_kosong'));
			$title = 'Print Table Stock Opname';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_stock_opname_prep', $output);
		}else{
			redirect('admin');
		}
	}
	
	public function set_kosong(){
		return "";
	}

	public function stock_opname()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$c = new grocery_CRUD();

			$c->set_subject('Mutasi Barang');
			$c->where('tb_stock_opname.deleted',0);
			$c->order_by('id_stock_opname','DESC');
			$c->set_table('tb_stock_opname');
			$c->unset_add();
			$c->unset_read();
			$c->display_as('nama_barang','Barang')
			  ->display_as('sku_barang','SKU Barang')
			  ->display_as('stok_database','Stok Awal')
			  ->display_as('stok_gudang','Stok Baru')
			  ->display_as('id_user','Username');
			$c->unset_columns('id_barang','created','updated','deleted');
			$c->unset_fields('created','updated','deleted','sku_barang','nama_barang','stok_database','stok_gudang','id_barang');
			$c->set_relation('id_user','tb_user','username');
			$c->callback_field('id_user',array($this,'set_value_user'));
			$c->required_fields('kode_barang','nama_barang','stok');
			$c->callback_delete(array($this,'delete_stock_opname'));
			$title = 'Mutasi Barang';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_stock_opname', $output);
		}else{
			redirect('admin');
		}
	}

	public function add_stock_opname()
	{
		if($this->session->userdata('logged_in')){
			$data['title'] = 'Add Stock Opname';
			$this->load->view('add_stock_opname', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_stock_opname($primary_key)
	{
		$this->db->update('tb_stock_opname', array('deleted'=>'1'),array('id_stock_opname'=>$primary_key));

		$query = $this->db->query('SELECT * FROM tb_stock_opname WHERE id_stock_opname='.$primary_key)->row();
		$id_barang = $query->id_barang;
		$stok_db = $query->stok_database;
		$stok_gd = $query->stok_gudang;
		$stok_delta = $stok_db - $stok_gd;

		$this->db->update('tb_barang',array('stok' => $stok_db),array('id_barang'=>$id_barang));
					$history = array(
						'id_barang' => $id_barang,
						'mod_stok' => $stok_delta,
						'tanggal' => date('Y-m-d H:i:s'),
						'keterangan' => 'Pembatalan Mutasi Barang'
					);
		
		$this->db->insert('tb_history_stok',$history);

		if($this->db->affected_rows()>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function stock_opname_baru()
	{
		if($this->session->userdata('logged_in')){
			$c = new grocery_CRUD();

			$c->set_subject('Stock Opname');
			$c->where('tb_stock_opname_baru.deleted',0);
			$c->order_by('id_stock_opname_baru','DESC');
			$c->set_table('tb_stock_opname_baru');
			$c->unset_columns('created','updated','deleted');
			$c->unset_fields('created','updated','deleted');
			$c->required_fields('tanggal','status');
			$c->columns('tanggal_stock_opname_baru','status','id_user','detail');
			$c->field_type('status','dropdown',array('Open' => 'Open', 'Close' => 'Close'));
			$c->callback_column('status',array($this,'status_label'))
			  ->callback_column('detail',array($this,'detail_opname'));
			$c->set_relation('id_user','tb_user','username');
			$c->display_as('id_user','Username');
			$c->callback_field('id_user',array($this,'set_value_user'));
			$c->callback_delete(array($this,'delete_stock_opname_baru'));
			$c->add_action('Detail', '', 'admin/detail_daftar_stock_opname_baru', 'fa-file');
			$title = 'Stock Opname';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_stock_opname_baru', $output);
		}else{
			redirect('/','refresh');
		}
	}

	public function set_value_user($value='',$primary_key=null)
	{
		return '<input type="hidden" value="'.$this->session->userdata('id_user').'" name="id_user">';
	}

	public function status_label($value,$row)
	{
		if($row->status == 'Open'){
			return '<div class="label label-success">'.$value.'</div>';
		}else{
			return '<div class="label label-danger">'.$value.'</div>';
		}
	}

	public function detail_opname($value,$row)
	{
		return '
		<a href="'.base_url().'index.php/admin/detail_stock_opname_baru/'.$row->id_stock_opname_baru.'" class="btn btn-default"><i class="fa fa-sign-in"></i> Detail Stock Opname</a>
		';
	}

	public function delete_stock_opname_baru($primary_key)
	{
		$this->db->update('tb_stock_opname_baru', array('deleted'=>'1'),array('id_stock_opname_baru'=>$primary_key));
		$this->db->update('tb_detail_stock_opname_baru', array('deleted' => '1'),array('id_stock_opname_baru' => $primary_key));

		$query = $this->db->query('SELECT * FROM tb_detail_stock_opname_baru WHERE id_stock_opname_baru='.$primary_key)->result();
		
		foreach ($query as $data) {

			$stok = $data->stok_database;
			$id_barang = $data->id_barang;
			$stok_gd = $data->stok_gudang;
			$stokdelta = $stok-$stok_gd;

			$this->db->update('tb_barang', array('stok' => $stok),array('id_barang' => $id_barang));

			$history = array(
				'id_barang' => $id_barang,
				'mod_stok' => $stokdelta,
				'tanggal' => date('Y-m-d H:i:s'),
				'keterangan' => 'Pembatalan stock opname' 
			);
		
			$this->db->insert('tb_history_stok',$history);
		}
	}

	public function detail_stock_opname_baru()
	{
		if($this->session->userdata('logged_in')){
			$c = new grocery_CRUD();

			$c->set_subject('Detail Stok Opname');
			$c->where('tb_detail_stock_opname_baru.deleted',0);
			$c->order_by('id_detail_stock_opname_baru','DESC');
			$c->where('tb_detail_stock_opname_baru.id_stock_opname_baru',$this->uri->segment(3));
			$c->set_table('tb_detail_stock_opname_baru');
			$c->unset_add();
			$c->unset_columns('created','updated','deleted','id_stock_opname_baru','id_barang');
			$c->unset_fields('created','updated','deleted','id_barang','sku_barang','nama_barang','stok_database','stok_gudang','id_stock_opname_baru');
			$c->display_as('id_user','Username')
				->display_as('stok_database','Stok Awal')
			  ->display_as('stok_gudang','Stok Baru');
			$c->callback_column('status',array($this,'status_label'))
			  ->callback_column('detail',array($this,'detail_opname'));
			$c->set_relation('id_user','tb_user','username');
			$c->callback_field('id_user',array($this,'set_value_user'));
			$c->callback_delete(array($this,'delete_detail_stock_opname_baru'));
			$title = 'Stock Opname';
			$this->load->vars( array('title' => $title));
			$output = $c->render();
			$this->load->view('data_detail_stock_opname_baru', $output);
		}else{
			redirect('/','refresh');
		}
	}

	public function add_detail_stock_opname_baru($id_stock_opname_baru)
	{
		if($this->session->userdata('logged_in')){
			$data['title'] = 'Add Barang for Detail Stock Opname';
			$this->load->view('add_detail_stock_opname_baru', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function detail_daftar_stock_opname_baru($id_stock_opname_baru)
	{
		if($this->session->userdata('logged_in')){
			$data['title'] = 'Detail Stock Opname';
			$data['stock'] = $this->Transaction_model->detail_stock_opname_baru($id_stock_opname_baru);
			$data['detail'] = $this->Transaction_model->detail_daftar_stock_opname_baru($id_stock_opname_baru);
			$this->load->view('report/detail_stock_opname_baru', $data);
		}else{
			redirect('admin','refresh');
		}
	}

	public function delete_detail_stock_opname_baru($primary_key)
	{
		return $this->db->update('tb_detail_stock_opname_baru', array('deleted'=>'1'),array('id_detail_stock_opname_baru'=>$primary_key));
	}
}

/* A wrapper to do organise item names & prices into columns */
class leftright
{
    private $left;
    private $right;
    private $dollarSign;

    public function __construct($left = '', $right = '', $dollarSign = false)
    {
        $this -> left = $left;
        $this -> right = $right;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 40;
        $leftCols = 40;
        /*if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }*/
        $left = str_pad($this -> left, $leftCols) ;

        //$sign = ($this -> dollarSign ? 'Rp ' : '');
        //$right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT); //this causes right justify
        $right = str_pad($this -> right, $rightCols, ' ', STR_PAD_RIGHT);
        return "$left$right\n";
    }
}

class item
{
	private $num;
	private $name;
	private $qty;
	private $harga;
	private $diskon;
	private $subtotal;
	
	public function __construct($num = '', $name = '', $qty = '', $harga = '', $diskon = '', $subtotal = '')
    {
        $this->num = $num;
        $this->name = $name;
        $this->qty = $qty;
        $this->harga = $harga;
        $this->diskon = $diskon;
        $this->subtotal = $subtotal;
    }

    public function __toString()
    {
        $cnum = 2;
        $cname = 28;
        $cqty = 9;
        $charga = 14;
        $cdiskon = 6;
        $csubtotal = 14;

		$num = str_pad($this->num, $cnum, ' ', STR_PAD_LEFT);
		$name = str_pad($this->name, $cname, ' ', STR_PAD_RIGHT);
		$qty = str_pad($this->qty, $cqty, ' ', STR_PAD_LEFT) ;
		$harga = str_pad($this->harga, $charga, ' ', STR_PAD_LEFT) ;
		$diskon = str_pad($this->diskon, $cdiskon, ' ', STR_PAD_LEFT) ;
		$subtotal = str_pad($this->subtotal, $csubtotal, ' ', STR_PAD_LEFT) ;

        return "|$num|$name|$qty|$harga|$diskon|$subtotal|\n";
    }
}

/* End of file Admin.php */
/* Location: ./application/controllers/Admin.php */
