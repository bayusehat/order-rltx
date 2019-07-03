<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model {

	public function get_scan_barang($scan_data)
	{
			 	$this->db->select('*');
				$this->db->like('nama_barang',$scan_data);
				$this->db->or_like('sku_barang',$scan_data);
				$this->db->where('deleted',0);
				$this->db->limit(20);
		return	$this->db->get('tb_barang')->result();
	}

    public function get_stok($id_barang)
    {
        return $this->db->select('stok')
                        ->where('id_barang',$id_barang)
                        ->get('tb_barang')
                        ->row();
        
    }

    public function get_harga($id_barang)
    {
        return $this->db->select('harga_modal,harga_jual')
                        ->where('id_barang',$id_barang)
                        ->get('tb_barang');
    }

	public function add_barang()
    {
    	$sku_barang = $this->input->post('sku_barang');
    	$nama_barang= $this->input->post('nama_barang');
    	$harga_modal= $this->input->post('harga_modal');
    	$harga_jual = $this->input->post('harga_jual');
    	$stok		= $this->input->post('stok_barang');
        $satuan     = $this->input->post('satuan');

    	$data = array(
    		'sku_barang' => $sku_barang,
    		'nama_barang'=> $nama_barang,
    		'harga_modal'=> $harga_modal,
    		'harga_jual' => $harga_jual,
    		'stok'		 => $stok,
            'satuan'     => $satuan 
    	);

    	$this->db->insert('tb_barang',$data);
    	$id_barang = $this->db->insert_id();

    	$history = array(
    		'id_barang' => $id_barang,
    		'mod_stok' => $stok,
    		'tanggal' => date('Y-m-d h:i:s'),
    		'keterangan' => 'Input barang baru' 
    	);

    	$this->db->insert('tb_history_stok',$history);
    	
    	if($this->db->affected_rows() > 0){
    		return TRUE;
    	}else{
    		return FALSE;
    	}	
    }

    public function update_barang()
    {
    	$id_barang	= $this->input->post('id_barang');
    	$sku_barang = $this->input->post('sku_barang');
    	$nama_barang= $this->input->post('nama_barang');
    	$harga_modal= $this->input->post('harga_modal');
    	$harga_jual = $this->input->post('harga_jual');
    	$stok		= $this->input->post('stok_barang');
    	$stok_tambah= $this->input->post('stok_tambah');
    	$stok_baru 	= $stok+$stok_tambah;
        $satuan     = $this->input->post('satuan'); 

    	$data = array(
    		'sku_barang' => $sku_barang,
    		'nama_barang'=> $nama_barang,
    		'harga_modal'=> $harga_modal,
    		'harga_jual' => $harga_jual,
    		'stok'		 => $stok_baru,
            'satuan'     => $satuan 
    	);

        if($stok_tambah != '0'){
        	$history = array(
        		'id_barang' => $id_barang,
        		'mod_stok' => $stok_tambah,
        		'tanggal' => date('Y-m-d h:i:s'),
        		'keterangan' => 'Penambahan stok' 
        	);

        	$this->db->insert('tb_history_stok',$history);
        }

        $get = $this->Transaction_model->get_harga($id_barang)->row();

        $get_harga_modal = $get->harga_modal;
        $get_harga_jual = $get->harga_jual;

        if($get_harga_modal != $harga_modal){
            $h_harga_modal = $harga_modal-$get_harga_modal;

            $this->db->update('tb_barang', array('harga_modal'=>$harga_modal),array('id_barang'=>$id_barang));

            $history = array(
                'id_barang' =>$id_barang,
                'mod_harga' => $h_harga_modal,
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Perubahan harga modal' 
            );
            $this->db->insert('tb_history_harga_barang', $history);
        }

        if($get_harga_jual != $harga_jual){
            $h_harga_jual = $harga_jual-$get_harga_jual;
            
            $this->db->update('tb_barang', array('harga_modal'=>$harga_jual),array('id_barang'=>$id_barang));

            $history = array(
                'id_barang' =>$id_barang,
                'mod_harga' => $h_harga_jual,
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Perubahan harga jual' 
            );
            $this->db->insert('tb_history_harga_barang', $history);
        }
    	

        $this->db->where('id_barang',$id_barang)
                 ->update('tb_barang',$data);

    	if($this->db->affected_rows() > 0){
    		return TRUE;
    	}else{
    		return FALSE;
    	}
    }

    public function nomor_jual()
    {
        $this->db->select('RIGHT(tb_penjualan.nomor_penjualan,5) as nomor_penjualan', FALSE);
        $this->db->order_by('nomor_penjualan','DESC');    
        $this->db->limit(1);    
        $query = $this->db->get('tb_penjualan');   
        if($query->num_rows() <> 0){      
   
               $data = $query->row();      
               $kode = intval($data->nomor_penjualan) + 1; 
          }
          else{      
               $kode = 1;
          } 
              $nomor_jual_max = str_pad($kode, 8, "0", STR_PAD_LEFT);    
              $nomor_tampil = "PJ-".$nomor_jual_max;
              return $nomor_tampil;  
    }

    public function simpan_penjualan()
    {
        $nomor_penjualan = $this->nomor_jual(); 	
		$tanggal_penjualan = $this->input->post('tanggal_penjualan');
        $tanggal_jatuh_tempo = $this->input->post('tanggal_jatuh_tempo');
		$id_payment = $this->input->post('id_payment');
        $metode = $this->input->post('metode');
		$id_sales = $this->input->post('id_sales'); 	
		$id_pelanggan = $this->input->post('id_pelanggan');
		$ppn = $this->input->post('ppn');
        $status = $this->input->post('status');
        $tanggal_lunas = $this->input->post('tanggal_lunas');
 		$total = $this->input->post('total');
		$kembali = $this->input->post('kembali');
		$bayar = $this->input->post('bayar');
        $nominal_ppn = $this->input->post('nominal_ppn');

		$data = array(
			// 'no_serial'	=> $no_serial,
			'nomor_penjualan' => $nomor_penjualan,
			'tanggal_penjualan' => $tanggal_penjualan,
            'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo,
			'id_payment' => $id_payment,
			'id_pelanggan' => $id_pelanggan,
			'id_sales' => $id_sales,
            'id_user' => $this->session->userdata('id_user'),
			'ppn' => $ppn,
            'status' => $status,
            'tanggal_lunas' => $tanggal_lunas,
            'metode' => $metode,
			'bayar' => $bayar,
			'total' => $total, 
			'sisa' => $kembali,
            'nominal_ppn' => $nominal_ppn
		);
		
		$this->db->insert('tb_penjualan',$data);
		
		$last_id = $this->db->insert_id();

		$detail = array();
		$history = array();
		$id_barang = $this->input->post('id_barang');
		$sku_barang = $this->input->post('sku_barang');
		$nama_barang = $this->input->post('nama_barang');
		$harga	= $this->input->post('harga');
		$jumlah = $this->input->post('quantity');
		$subtotal = $this->input->post('subtotal');
        $diskon = $this->input->post('diskon');
		
		foreach($id_barang as $i => $item){
			$detail[] = array(
				'id_barang' => $id_barang[$i],
				'sku_barang' => $sku_barang[$i],
				'nama_barang' => $nama_barang[$i],
				'harga' => $harga[$i],
                'diskon' => $diskon[$i],
				'jumlah' => $jumlah[$i],
				'subtotal' => $subtotal[$i],
				'id_penjualan' => $last_id
			);

			$mod = -1*$jumlah[$i];

			$history[] = array(
				'id_barang' => $id_barang[$i],
				'mod_stok' => $mod,
				'tanggal' => date('Y-m-d h:i:s'),
				'keterangan' => 'Penjualan',
                'id_penjualan' => $last_id
			);
			
			//UPDATE HARGA DI MASTER BARANG APABILA LEBIH MAHAL, NANTINYA SAVE JUGA DI HISTORY ARUS HARGA
			$currentbarang = $this->db->query("select harga_jual from tb_barang where id_barang = ".$id_barang[$i])->result();
			if(count($currentbarang) > 0){
				$current_barang_harga = $currentbarang[0]->harga_jual;
				if($current_barang_harga < $harga[$i])
					$this->db->query("update tb_barang set harga_jual = ".$harga[$i]." where id_barang = ".$id_barang[$i]);
			}

		}
	
		$this->db->insert_batch('tb_detail_penjualan',$detail,$last_id);
		$this->db->insert_batch('tb_history_stok',$history);

		$opencash = array(
    			'type_register' => 'auto',
    			'keterangan' => 'Open cash register nota '.$nomor_penjualan.' tanggal '.$tanggal_penjualan
    		);

    	$this->db->insert('tb_cash_register_history',$opencash);

        $no_giro = $this->input->post('no_giro');
        $nominal_giro = $this->input->post('nominal_giro');
        $tanggal_jatuh_tempo_giro = $this->input->post('tanggal_jatuh_tempo_giro');
        $nama_bank = $this->input->post('nama_bank');
        $keterangan_giro = $this->input->post('keterangan_giro');
        $status_giro = $this->input->post('status_giro');

        if($metode == "Giro"){
            $giro = array(
                'no_giro' => $no_giro,
                'nominal_giro' => $nominal_giro,
                'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo_giro,
                'nama_bank' => $nama_bank,
                'keterangan_giro' => $keterangan_giro,
                'status_giro' => $status_giro,
                'id_penjualan' => $last_id  
            );

            $this->db->insert('tb_giro_masuk', $giro);
        }else{
            $this->db->update('tb_penjualan',array('metode'=> '-'),array('id_penjualan'=>$last_id));
        }

			if($this->db->affected_rows() > 0){
				return TRUE;
			}else{
				return FALSE;
			}
    }

    public function edit_penjualan($id_penjualan)
    {     
        $tanggal_penjualan = $this->input->post('tanggal_penjualan');
        $tanggal_jatuh_tempo = $this->input->post('tanggal_jatuh_tempo');
        $id_payment = $this->input->post('id_payment');
        $metode = $this->input->post('metode');
        $id_sales = $this->input->post('id_sales');     
        $id_pelanggan = $this->input->post('id_pelanggan');
        $ppn = $this->input->post('ppn');
        $status = $this->input->post('status');
        $tanggal_lunas = $this->input->post('tanggal_lunas');
        $total = $this->input->post('total');
        $kembali = $this->input->post('kembali');
        $bayar = $this->input->post('bayar');

        $data = array(
            'tanggal_penjualan' => $tanggal_penjualan,
            'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo,
            'id_payment' => $id_payment,
            'id_pelanggan' => $id_pelanggan,
            'id_sales' => $id_sales,
            'id_user' => $this->session->userdata('id_user'),
            'ppn' => $ppn,
            'status' => $status,
            'tanggal_lunas' => $tanggal_lunas,
            'metode' => $metode,
            'total' => $total,
            'bayar' => $bayar,
            'sisa' => $kembali
        );

        $this->db->where('id_penjualan', $id_penjualan)
                 ->update('tb_penjualan',$data);

        $no_giro = $this->input->post('no_giro');
        $nominal_giro = $this->input->post('nominal_giro');
        $tanggal_jatuh_tempo_giro = $this->input->post('tanggal_jatuh_tempo_giro');
        $nama_bank = $this->input->post('nama_bank');
        $keterangan_giro = $this->input->post('keterangan_giro');
        $status_giro = $this->input->post('status_giro');

        if($metode == "Giro"){
            $giro = array(
                'no_giro' => $no_giro,
                'nominal_giro' => $nominal_giro,
                'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo_giro,
                'nama_bank' => $nama_bank,
                'keterangan_giro' => $keterangan_giro,
                'status_giro' => $status_giro,
                'id_penjualan' => $id_penjualan  
            );

            $this->db->insert('tb_giro_masuk', $giro);
        }else{
            $this->db->update('tb_penjualan',array('metode'=> '-'),array('id_penjualan'=>$id_penjualan));
        }

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function detail_penjualan($id_penjualan)
    {
        return $this->db->select('tb_penjualan.*,tb_sales.nama_sales,tb_master_payment.id_payment,tb_master_payment.nama_payment,tb_master_payment.cash_payment,tb_pelanggan.nama_pelanggan,tb_user.username')
                        ->from('tb_penjualan')
                        ->join('tb_sales','tb_sales.id_sales=tb_penjualan.id_sales')
                        ->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
                        ->join('tb_user','tb_user.id_user=tb_penjualan.id_user')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_penjualan.id_payment')
                        ->where('tb_penjualan.id_penjualan',$id_penjualan)
                        ->get()
                        ->row();
    }

    public function nomor_beli()
    {
        $this->db->select('RIGHT(tb_pembelian.nomor_pembelian,5) as nomor_pembelian', FALSE);
        $this->db->order_by('nomor_pembelian','DESC');    
        $this->db->limit(1);    
        $query = $this->db->get('tb_pembelian');   
        if($query->num_rows() <> 0){      
   
               $data = $query->row();      
               $kode = intval($data->nomor_pembelian) + 1; 
          }
          else{      
               $kode = 1;
          } 
              $nomor_beli_max = str_pad($kode, 8, "0", STR_PAD_LEFT);    
              $nomor_tampil = "PB-".$nomor_beli_max;
              return $nomor_tampil;  
    }

    public function simpan_pembelian()
    {
		$nomor_pembelian = $this->nomor_beli(); 	
		$tanggal_pembelian = $this->input->post('tanggal_pembelian');
        $tanggal_jatuh_tempo = $this->input->post('tanggal_jatuh_tempo');
		$id_payment = $this->input->post('id_payment'); 	
		$id_supplier = $this->input->post('id_supplier');
		$no_nota_supplier = $this->input->post('no_nota_supplier');
        $metode = $this->input->post('metode');
		$total = $this->input->post('total');
		$kembali = $this->input->post('kembali');
		$bayar = $this->input->post('bayar');
		$ppn = $this->input->post('ppn');
        $status = $this->input->post('status');
        $tanggal_lunas = $this->input->post('tanggal_lunas');
        $nominal_ppn = $this->input->post('nominal_ppn');


		$data = array(
            // 'no_serial' => $no_serial,
			'nomor_pembelian' => $nomor_pembelian,
			'tanggal_pembelian' => $tanggal_pembelian,
            'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo,
			'id_payment' => $id_payment,
			'id_supplier' => $id_supplier,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'tanggal_lunas' => $tanggal_lunas,
            'metode' => $metode,
			'ppn' => $ppn,
			'bayar' => $bayar,
			'total' => $total, 
			'no_nota_supplier' => $no_nota_supplier,
			'sisa' => $kembali,
            'nominal_ppn' => $nominal_ppn
		);

		$this->db->insert('tb_pembelian',$data);
		$last_id = $this->db->insert_id();

		$detail = array();
		$history = array();
		$id_barang = $this->input->post('id_barang');
		$sku_barang = $this->input->post('sku_barang');
		$nama_barang = $this->input->post('nama_barang');
		$harga	= $this->input->post('harga');
		$jumlah = $this->input->post('quantity');
		$subtotal = $this->input->post('subtotal');
        $diskon = $this->input->post('diskon');
		
		foreach($id_barang as $i => $item){
			$detail[] = array(
				'id_barang' => $id_barang[$i],
				'sku_barang' => $sku_barang[$i],
				'nama_barang' => $nama_barang[$i],
				'harga' => $harga[$i],
                'diskon' => $diskon[$i],
				'jumlah' => $jumlah[$i],
				'subtotal' => $subtotal[$i],
				'id_pembelian' => $last_id
			);

			$mod = $jumlah[$i];

			$history[] = array(
				'id_barang' => $id_barang[$i],
				'mod_stok' => $mod,
				'tanggal' => date('Y-m-d h:i:s'),
				'keterangan' => 'Pembelian',
                'id_pembelian' => $last_id
			);
			
			//UPDATE HARGA DI MASTER BARANG APABILA LEBIH MAHAL, NANTINYA SAVE JUGA DI HISTORY ARUS HARGA
			//GUNAKAN AVERAGING !!!!!!!!
			$currentbarang = $this->db->query("select harga_modal from tb_barang where id_barang = ".$id_barang[$i])->result();
			if(count($currentbarang) > 0){
				$current_barang_harga = $currentbarang[0]->harga_modal;
				if($current_barang_harga < $harga[$i])
					$this->db->query("update tb_barang set harga_modal = ".$harga[$i]." where id_barang = ".$id_barang[$i]);
			}

		}
	
		$this->db->insert_batch('tb_detail_pembelian',$detail,$last_id);
		$this->db->insert_batch('tb_history_stok',$history);

		$opencash = array(
    			'type_register' => 'auto',
    			'keterangan' => 'Open cash register nota '.$nomor_pembelian.' tanggal '.$tanggal_pembelian
    		);

    	$this->db->insert('tb_cash_register_history',$opencash);

        $no_giro = $this->input->post('no_giro');
        $nominal_giro = $this->input->post('nominal_giro');
        $tanggal_jatuh_tempo_giro = $this->input->post('tanggal_jatuh_tempo_giro');
        $nama_bank = $this->input->post('nama_bank');
        $keterangan_giro = $this->input->post('keterangan_giro');
        $status_giro = $this->input->post('status_giro');

        if($metode == "Giro"){
            $giro = array(
                'no_giro' => $no_giro,
                'nominal_giro' => $nominal_giro,
                'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo_giro,
                'nama_bank' => $nama_bank,
                'keterangan_giro' => $keterangan_giro,
                'status' => $status_giro,
                'id_pembelian' => $last_id  
            );

            $this->db->insert('tb_giro_keluar', $giro);
        }else{
            $this->db->update('tb_pembelian',array('metode'=>'-'),array('id_pembelian'=>$last_id));
        }

			if($this->db->affected_rows() > 0){
				return TRUE;
			}else{
				return FALSE;
			}
    }

    public function edit_pembelian($id_pembelian)
    {
        $tanggal_pembelian = $this->input->post('tanggal_pembelian');
        $tanggal_jatuh_tempo = $this->input->post('tanggal_jatuh_tempo');
        $id_payment = $this->input->post('id_payment');     
        $id_supplier = $this->input->post('id_supplier');
        $no_nota_supplier = $this->input->post('no_nota_supplier');
        $metode = $this->input->post('metode');
        $total = $this->input->post('total');
        $kembali = $this->input->post('kembali');
        $bayar = $this->input->post('bayar');
        $ppn = $this->input->post('ppn');
        $status = $this->input->post('status');
        $tanggal_lunas = $this->input->post('tanggal_lunas');

        $data = array(
            'tanggal_pembelian' => $tanggal_pembelian,
            'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo,
            'id_payment' => $id_payment,
            'id_supplier' => $id_supplier,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'tanggal_lunas' => $tanggal_lunas,
            'metode' => $metode,
            'ppn' => $ppn,
            'bayar' => $bayar,
            'total' => $total,
            'no_nota_supplier' => $no_nota_supplier, 
            'sisa' => $kembali
        );

        $this->db->where('id_pembelian', $id_pembelian)
                 ->update('tb_pembelian',$data);

        $no_giro = $this->input->post('no_giro');
        $nominal_giro = $this->input->post('nominal_giro');
        $tanggal_jatuh_tempo_giro = $this->input->post('tanggal_jatuh_tempo_giro');
        $nama_bank = $this->input->post('nama_bank');
        $keterangan_giro = $this->input->post('keterangan_giro');
        $status_giro = $this->input->post('status_giro');

        if($metode == "Giro"){
            $giro = array(
                'no_giro' => $no_giro,
                'nominal_giro' => $nominal_giro,
                'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo_giro,
                'nama_bank' => $nama_bank,
                'keterangan_giro' => $keterangan_giro,
                'status' => $status_giro,
                'id_pembelian' => $id_pembelian  
            );

            $this->db->insert('tb_giro_keluar', $giro);
        }else{
            $this->db->update('tb_pembelian',array('metode'=>'-'),array('id_pembelian'=>$id_pembelian));
        }

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function detail_pembelian($id_pembelian)
    {
        return $this->db->select('tb_pembelian.*,tb_supplier.nama_supplier,tb_master_payment.id_payment,tb_master_payment.nama_payment,tb_master_payment.cash_payment,tb_user.username')
                        ->from('tb_pembelian')
                        ->join('tb_supplier','tb_supplier.id_supplier=tb_pembelian.id_supplier')
                        ->join('tb_user','tb_user.id_user=tb_pembelian.id_user')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_pembelian.id_payment')
                        ->where('tb_pembelian.id_pembelian',$id_pembelian)
                        ->get()
                        ->row();
    }

    public function get_penjualan_stats()
    {
    	$query = $this->db->query("SELECT count(nomor_penjualan) as m, DATE_FORMAT(tanggal_penjualan,'%Y-%m')as d FROM tb_penjualan GROUP BY DATE_FORMAT(tanggal_penjualan,'%Y-%m')");

		if($query->num_rows() > 0){
			foreach ($query->result() as $data) {
				$hasil[] = $data;
			}
			return $hasil;
		}
    }

    public function get_pembelian_stats()
    {
    	$query = $this->db->query("SELECT count(nomor_pembelian) as m, DATE_FORMAT(tanggal_pembelian,'%Y-%m')as d FROM tb_pembelian GROUP BY DATE_FORMAT(tanggal_pembelian,'%Y-%m')");

		if($query->num_rows() > 0){
			foreach ($query->result() as $data) {
				$hasil[] = $data;
			}
			return $hasil;
		}
    }

    public function get_sales_progress()
    {
    	$query = $this->db->query("SELECT tb_sales.*, count(tb_penjualan.id_sales) as jumlah_penjualan from tb_sales left join tb_penjualan on (tb_sales.id_sales = tb_penjualan.id_sales) group by tb_sales.id_sales order by count(tb_penjualan.id_sales) DESC LIMIT 1")->result();

    	return $query;
    }

    public function get_supplier_progress()
    {
        $query = $this->db->query("SELECT tb_supplier.*, count(tb_pembelian.id_supplier) as jumlah_pembelian from tb_supplier left join tb_pembelian on (tb_supplier.id_supplier = tb_pembelian.id_supplier) group by tb_supplier.id_supplier order by count(tb_supplier.id_supplier) DESC LIMIT 1")->result();

        return $query;
    }

    public function get_supplier_progress_data()
    {
        $query = $this->db->query("SELECT tb_supplier.*, count(tb_pembelian.id_supplier) as jumlah_pembelian,sum(tb_pembelian.total) as total_beli from tb_supplier left join tb_pembelian on (tb_supplier.id_supplier = tb_pembelian.id_supplier) group by tb_supplier.id_supplier order by count(tb_supplier.id_supplier) DESC")->result(); 

        return $query;
    }

    public function get_sales_progress_data()
    {
        $query = $this->db->query("SELECT tb_sales.*, count(tb_penjualan.id_sales) as jumlah_penjualan, sum(tb_penjualan.total) as omset from tb_sales left join tb_penjualan on (tb_sales.id_sales = tb_penjualan.id_sales) group by tb_sales.id_sales order by count(tb_penjualan.id_sales) DESC")->result();

        return $query;
    }

    public function get_pelanggan_progress_data()
    {
        $query = $this->db->query("SELECT tb_pelanggan.*, count(tb_penjualan.id_pelanggan) as jumlah_penjualan, sum(tb_penjualan.total) as omset from tb_pelanggan left join tb_penjualan on (tb_pelanggan.id_pelanggan = tb_penjualan.id_pelanggan) group by tb_pelanggan.id_pelanggan order by count(tb_penjualan.id_pelanggan) DESC")->result();

        return $query;
    }

    public function retur_penjualan($id_penjualan)
    {
    	return $this->db->select('tb_penjualan.*,tb_sales.nama_sales,tb_pelanggan.nama_pelanggan,tb_master_payment.nama_payment')
    					->from('tb_penjualan')
    					->join('tb_sales','tb_sales.id_sales=tb_penjualan.id_sales')
    					->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
    					->join('tb_master_payment','tb_master_payment.id_payment=tb_penjualan.id_payment')
    					->where('tb_penjualan.id_penjualan',$id_penjualan)
    					->get()
    					->row();
    }

    public function retur_pembelian($id_pembelian)
    {
        return $this->db->select('tb_pembelian.*,tb_supplier.nama_supplier,tb_master_payment.nama_payment')
                        ->from('tb_pembelian')
                        ->join('tb_supplier','tb_supplier.id_supplier=tb_pembelian.id_supplier')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_pembelian.id_payment')
                        ->where('tb_pembelian.id_pembelian',$id_pembelian)
                        ->get()
                        ->row();
    }

    public function simpan_retur_penjualan()
    {
    	$nomor_retur = $this->input->post('nomor_retur_penjualan');
    	$tanggal_retur = $this->input->post('tanggal_retur_penjualan');
    	$id_penjualan = $this->input->post('id_penjualan');
    	$total_retur = $this->input->post('total_retur');
    	$sisa_tagihan = $this->input->post('sisa_tagihan');
    	$keterangan = $this->input->post('keterangan');
    	$no_serial = $this->input->post('no_serial');

    	$data = array(
    		'nomor_retur_penjualan' => $nomor_retur,
    		'no_serial' => $no_serial,
    		'tanggal_retur_penjualan' => $tanggal_retur,
    		'id_penjualan' => $id_penjualan,
    		'keterangan' => $keterangan,
    		'sisa_tagihan' => $sisa_tagihan,
    		'total_retur' => $total_retur,
            'id_user' => $this->session->userdata('id_user')
    	 );

    	$this->db->insert('tb_retur_penjualan',$data);
    	$last_id = $this->db->insert_id();

    	$detail = array();
    	$jual_update = array();
        $stok_barang = array();
        $history = array();
    	$id_barang = $this->input->post('id_barang');
    	$sku_barang = $this->input->post('sku_barang');
    	$nama_barang = $this->input->post('nama_barang');
    	$harga = $this->input->post('harga_barang');
    	$qty_jual = $this->input->post('qty_jual');
    	$qty_retur = $this->input->post('qty_retur');
    	$subtotal = $this->input->post('subtotal_retur');
    	$id_detail_penjualan = $this->input->post('id_detail_penjualan');
    	

    	foreach($id_barang as $i => $item) { 

            $new_qty_jual[$i] = $qty_jual[$i] - $qty_retur[$i];

    		$detail[] = array(
    			'id_barang' => $id_barang[$i],
    			'sku_barang' => $sku_barang[$i],
    			'nama_barang' => $nama_barang[$i],
    			'harga' => $harga[$i],
    			'jumlah_jual' => $new_qty_jual[$i],
    			'jumlah_retur' => $qty_retur[$i],
    			'subtotal_retur' => $subtotal[$i],
    			'id_retur_penjualan' => $last_id,
    			'id_detail_penjualan' => $id_detail_penjualan[$i]
    		 );

			$new_qty[$i] = -1*$qty_retur[$i] + $qty_jual[$i];
            $new_subtotal[$i] = $new_qty[$i]*$harga[$i];

            // if($diskon[$i] > 0){
            //     $new_subtotal[$i] = ($diskon[$i]/100) * $new_subtotal[$i];
            // }else{
            //     $new_subtotal[$i] = $new_subtotal[$i];
            // }
	    	
    		$jual_update[] = array(
    			'id_detail_penjualan' => $id_detail_penjualan[$i],
    			'jumlah' => $new_qty[$i],
    			'subtotal' => $new_subtotal[$i]
    		);
            //Reverse stok ke master barang
            $currentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang[$i])->row();
            $newcurrent = $currentStok->stok;
            $newStok[$i] = $currentStok->stok + $qty_retur[$i];

            $stok_barang[] = array(
                'id_barang' => $id_barang[$i],
                'stok' => $newStok[$i]
            );
            //history stok
            $history[] = array(
                'id_barang' => $id_barang[$i],
                'mod_stok' => $qty_retur[$i],
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Retur penjualan barang',
                'id_penjualan' => $id_penjualan,
                'id_pembelian' => 0
             );
    	}

    	$this->db->insert_batch('tb_detail_retur_penjualan',$detail,$last_id);
    	$this->db->update_batch('tb_detail_penjualan',$jual_update,'id_detail_penjualan');
        $this->db->update_batch('tb_barang',$stok_barang,'id_barang');
        $this->db->insert_batch('tb_history_stok', $history);

        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_penjualan WHERE id_penjualan='.$id_penjualan.' AND deleted=0')->result();
        foreach ($getTotal as $tot) {
            $total = $tot->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_penjualan WHERE id_penjualan='.$id_penjualan)->row();

        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
             $getFinalTotal = $total;
             $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_penjualan', array('nominal_ppn'=>$hitungppn,'total'=>$getFinalTotal),array('id_penjualan'=>$id_penjualan));

    	if($this->db->affected_rows() > 0){
    		return TRUE;
    	}else{
    		return FALSE;
    	}
   	}

   	public function retured_items($id_penjualan)
   	{
   		return $this->db->select('tb_retur_penjualan.*')
   						->where('tb_retur_penjualan.id_penjualan',$id_penjualan)
   						->get('tb_retur_penjualan')
   						->result();
   	}

   	public function retur_penjualan_print($id_retur_penjualan)
   	{
   		return $this->db->select('tb_retur_penjualan.*,tb_detail_retur_penjualan.*,tb_penjualan.nomor_penjualan,tb_penjualan.id_penjualan,tb_penjualan.total,tb_penjualan.tanggal_penjualan,tb_pelanggan.nama_pelanggan,tb_user.username')
   						->from('tb_retur_penjualan')
   						->join('tb_detail_retur_penjualan','tb_detail_retur_penjualan.id_retur_penjualan=tb_retur_penjualan.id_retur_penjualan','left')
   						->join('tb_penjualan','tb_penjualan.id_penjualan=tb_retur_penjualan.id_penjualan')
                        ->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
                        ->join('tb_user','tb_user.id_user=tb_retur_penjualan.id_user')
   						->where('tb_retur_penjualan.id_retur_penjualan',$id_retur_penjualan)
   						->get()
   						->row();
   	}

   	public function update_retur_penjualan($id_retur_penjualan)
   	{
   		$nomor_retur = $this->input->post('nomor_retur_penjualan');
    	$tanggal_retur = $this->input->post('tanggal_retur_penjualan');
    	$id_penjualan = $this->input->post('id_penjualan');
    	$total_retur = $this->input->post('total_retur');
    	$sisa_tagihan = $this->input->post('sisa_tagihan');
    	$keterangan = $this->input->post('keterangan');
    	$no_serial = $this->input->post('no_serial');

    	$data = array(
    		'nomor_retur_penjualan' => $nomor_retur,
    		'no_serial' => $no_serial,
    		'tanggal_retur_penjualan' => $tanggal_retur,
    		'id_penjualan' => $id_penjualan,
    		'keterangan' => $keterangan,
    		'sisa_tagihan' => $sisa_tagihan,
    		'total_retur' => $total_retur
    	 );

    	$this->db->where('id_retur_penjualan',$id_retur_penjualan)
    			 ->update('tb_retur_penjualan',$data);

    	// $last_id = $this->db->insert_id();

    	$detail = array();
    	$jual_update = array();
        $stok_barang = array();
        $history = array();
    	$id_barang = $this->input->post('id_barang');
    	$sku_barang = $this->input->post('sku_barang');
    	$nama_barang = $this->input->post('nama_barang');
    	$harga = $this->input->post('harga_barang');
    	$qty_jual = $this->input->post('qty_jual');
    	$qty_retur = $this->input->post('qty_retur');
        $qty_ubah = $this->input->post('qty_ubah');
    	$subtotal = $this->input->post('subtotal_retur');
    	$id_detail_retur_penjualan = $this->input->post('id_detail_retur_penjualan');
    	$id_detail_penjualan = $this->input->post('id_detail_penjualan');
    	

    	foreach($id_barang as $i => $item) { 
            $new_qty_retur[$i] = $qty_retur[$i] - $qty_ubah[$i];
            $new_qty_jual[$i] = $qty_jual[$i] + $qty_ubah[$i];
            $detail[] = array(
                'id_detail_retur_penjualan' => $id_detail_retur_penjualan[$i],
                'id_barang' => $id_barang[$i],
                'sku_barang' => $sku_barang[$i],
                'nama_barang' => $nama_barang[$i],
                'harga' => $harga[$i],
                'jumlah_jual' => $new_qty_jual[$i],
                'jumlah_retur' => $new_qty_retur[$i],
                'subtotal_retur' => $subtotal[$i],
                'id_retur_penjualan' => $id_retur_penjualan
             );

            $new_qty[$i] = $new_qty_retur[$i] + $qty_jual[$i];
	    	$new_subtotal[$i] = $new_qty_jual[$i]*$harga[$i];

    		$jual_update[] = array(
    			'id_detail_penjualan' => $id_detail_penjualan[$i],
    			'jumlah' => $new_qty_jual[$i],
    			'subtotal' => $new_subtotal[$i]
    		);

            //Kurangi stok master barang
            $currentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang[$i])->row();
            $newcurrent = $currentStok->stok;
            $newStok[$i] = $currentStok->stok - $qty_ubah[$i];

            $stok_barang[] = array(
                'id_barang' => $id_barang[$i],
                'stok' => $newStok[$i]
            );

            //History Stok
            $history[] = array(
                'id_barang' => $id_barang[$i],
                'mod_stok' => $qty_ubah[$i],
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Pembatalan retur penjualan barang',
                'id_penjualan' => $id_penjualan,
                'id_pembelian' => 0
             );
    	}

        if($total_retur == 0){
            $this->db->update('tb_retur_penjualan',array('deleted' => '1'),array('id_retur_penjualan' => $id_retur_penjualan));
            $this->db->update('tb_detail_retur_penjualan',array('deleted'=>'1'),array('id_retur_penjualan' => $id_retur_penjualan));
        }else{
            $this->db->update('tb_retur_penjualan',array('deleted' => '0'),array('id_retur_penjualan' => $id_retur_penjualan));
            $this->db->update('tb_detail_retur_penjualan',array('deleted'=>'0'),array('id_retur_penjualan' => $id_retur_penjualan));
        }

    	$this->db->update_batch('tb_detail_retur_penjualan',$detail,'id_detail_retur_penjualan');
    	$this->db->update_batch('tb_detail_penjualan',$jual_update,'id_detail_penjualan');
        $this->db->update_batch('tb_barang',$stok_barang,'id_barang');
        $this->db->insert_batch('tb_history_stok',$history);
        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_penjualan WHERE id_penjualan='.$id_penjualan.' AND deleted=0')->result();
        foreach ($getTotal as $tot) {
            $total = $tot->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_penjualan WHERE id_penjualan='.$id_penjualan)->row();

        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
             $getFinalTotal = $total;
             $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_penjualan', array('nominal_ppn'=>$hitungppn,'total'=>$getFinalTotal),array('id_penjualan'=>$id_penjualan));

    	if($this->db->affected_rows() > 0){
    		return TRUE;
    	}else{
    		return FALSE;
    	}
   	}

   	public function update_retured_items($id_retur_penjualan)
   	{
   		return $this->db->select('tb_penjualan.*,tb_retur_penjualan.*,tb_pelanggan.nama_pelanggan')
   						->from('tb_retur_penjualan')
   						->join('tb_penjualan','tb_penjualan.id_penjualan=tb_retur_penjualan.id_penjualan')
   						->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
   						->where('tb_retur_penjualan.id_retur_penjualan',$id_retur_penjualan)
   						->get()
   						->row();
   	}

    public function update_retured_items_beli($id_retur_pembelian)
    {
        return $this->db->select('tb_pembelian.*,tb_retur_pembelian.*,tb_supplier.nama_supplier')
                        ->from('tb_retur_pembelian')
                        ->join('tb_pembelian','tb_pembelian.id_pembelian=tb_retur_pembelian.id_pembelian')
                        ->join('tb_supplier','tb_supplier.id_supplier=tb_pembelian.id_supplier')
                        ->where('tb_retur_pembelian.id_retur_pembelian',$id_retur_pembelian)
                        ->get()
                        ->row();
    }

    public function simpan_retur_pembelian()
    {
        $nomor_retur = $this->input->post('nomor_retur_pembelian');
        $tanggal_retur = $this->input->post('tanggal_retur_pembelian');
        $id_pembelian = $this->input->post('id_pembelian');
        $total_retur = $this->input->post('total_retur_pembelian');
        $sisa_tagihan = $this->input->post('sisa_tagihan');
        $keterangan = $this->input->post('keterangan');
        $no_serial = $this->input->post('no_serial');

        $data = array(
            'no_retur_pembelian' => $nomor_retur,
            'no_serial' => $no_serial,
            'tanggal_retur_pembelian' => $tanggal_retur,
            'id_pembelian' => $id_pembelian,
            'keterangan' => $keterangan,
            'sisa_tagihan' => $sisa_tagihan,
            'total_retur_pembelian' => $total_retur,
            'id_user' => $this->session->userdata('id_user')
         );

        $this->db->insert('tb_retur_pembelian',$data);
        $last_id = $this->db->insert_id();

        $detail = array();
        $beli_update = array();
        $stok_barang = array();
        $history = array();
        $id_barang = $this->input->post('id_barang');
        $sku_barang = $this->input->post('sku_barang');
        $nama_barang = $this->input->post('nama_barang');
        $harga = $this->input->post('harga_barang');
        $qty_beli = $this->input->post('qty_beli');
        $qty_retur = $this->input->post('qty_retur');
        $subtotal = $this->input->post('subtotal_retur');
        $id_detail_pembelian = $this->input->post('id_detail_pembelian');
        

        foreach($id_barang as $i => $item) { 

            $new_qty_beli[$i] = $qty_beli[$i] - $qty_retur[$i];

            $detail[] = array(
                'id_barang' => $id_barang[$i],
                'sku_barang' => $sku_barang[$i],
                'nama_barang' => $nama_barang[$i],
                'harga' => $harga[$i],
                'jumlah_beli' => $new_qty_beli[$i],
                'jumlah_retur' => $qty_retur[$i],
                'subtotal_retur' => $subtotal[$i],
                'id_retur_pembelian' => $last_id,
                'id_detail_pembelian' => $id_detail_pembelian[$i],
             );

            $new_qty[$i] = -1*$qty_retur[$i] + $qty_beli[$i];
            $new_subtotal[$i] = $new_qty[$i]*$harga[$i];

            $beli_update[] = array(
                'id_detail_pembelian' => $id_detail_pembelian[$i],
                'jumlah' => $new_qty[$i],
                'subtotal' => $new_subtotal[$i]
            );

            $currentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang[$i])->row();
            $newcurrent = $currentStok->stok;
            $newStok[$i] = $currentStok->stok - $qty_retur[$i];

            $stok_barang[] = array(
                'id_barang' => $id_barang[$i],
                'stok' => $newStok[$i]
            );

            $history[] = array(
                'id_barang' => $id_barang[$i],
                'mod_stok' => $qty_retur[$i],
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Retur pembelian barang',
                'id_penjualan' => 0,
                'id_pembelian' => $id_pembelian
             );
        }

        $this->db->insert_batch('tb_detail_retur_pembelian',$detail,$last_id);
        $this->db->update_batch('tb_detail_pembelian',$beli_update,'id_detail_pembelian');
        $this->db->update_batch('tb_barang',$stok_barang,'id_barang');
        $this->db->insert_batch('tb_history_stok', $history);

        //Total Pembelian Temp
        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_pembelian WHERE id_pembelian='.$id_pembelian.' AND deleted=0')->result();
        foreach ($getTotal as $tot) {
            $total = $tot->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_pembelian WHERE id_pembelian='.$id_pembelian)->row();

        //Apabila ada PPN dan hitung final total
        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
             $getFinalTotal = $total;
             $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_pembelian', array('nominal_ppn'=>$hitungppn,'total'=>$getFinalTotal),array('id_pembelian'=>$id_pembelian));

        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function retured_items_beli($id_pembelian)
    {
        return $this->db->select('tb_retur_pembelian.*')
                        ->where('tb_retur_pembelian.id_pembelian',$id_pembelian)
                        ->get('tb_retur_pembelian')
                        ->result();
    }

    public function retur_pembelian_print($id_retur_pembelian)
    {
        return $this->db->select('tb_retur_pembelian.*,tb_detail_retur_pembelian.*,tb_pembelian.nomor_pembelian,tb_pembelian.id_pembelian,tb_pembelian.total,tb_pembelian.tanggal_pembelian,tb_supplier.nama_supplier,tb_user.username')
                        ->from('tb_retur_pembelian')
                        ->join('tb_detail_retur_pembelian','tb_detail_retur_pembelian.id_retur_pembelian=tb_retur_pembelian.id_retur_pembelian','left')
                        ->join('tb_pembelian','tb_pembelian.id_pembelian=tb_retur_pembelian.id_pembelian')
                        ->join('tb_supplier','tb_supplier.id_supplier=tb_pembelian.id_supplier')
                        ->join('tb_user','tb_user.id_user=tb_retur_pembelian.id_user')
                        ->where('tb_retur_pembelian.id_retur_pembelian',$id_retur_pembelian)
                        ->get()
                        ->row();
    }

    public function update_retur_pembelian($id_retur_pembelian)
    {
        $nomor_retur = $this->input->post('nomor_retur_pembelian');
        $tanggal_retur = $this->input->post('tanggal_retur_pembelian');
        $id_pembelian = $this->input->post('id_pembelian');
        $total_retur = $this->input->post('total_retur_pembelian');
        $sisa_tagihan = $this->input->post('sisa_tagihan');
        $keterangan = $this->input->post('keterangan');
        $no_serial = $this->input->post('no_serial');

        $data = array(
            'no_retur_pembelian' => $nomor_retur,
            'no_serial' => $no_serial,
            'tanggal_retur_pembelian' => $tanggal_retur,
            'id_pembelian' => $id_pembelian,
            'keterangan' => $keterangan,
            'sisa_tagihan' => $sisa_tagihan,
            'total_retur_pembelian' => $total_retur
         );

        $this->db->where('id_retur_pembelian',$id_retur_pembelian)
                 ->update('tb_retur_pembelian',$data);
        // $last_id = $this->db->insert_id();

        $detail = array();
        $beli_update = array();
        $stok_barang = array();
        $history = array();
        $id_barang = $this->input->post('id_barang');
        $sku_barang = $this->input->post('sku_barang');
        $nama_barang = $this->input->post('nama_barang');
        $harga = $this->input->post('harga_barang');
        $qty_beli = $this->input->post('qty_beli');
        $qty_retur = $this->input->post('qty_retur');
        $qty_ubah = $this->input->post('qty_ubah');
        $subtotal = $this->input->post('subtotal_retur');
        $id_detail_pembelian = $this->input->post('id_detail_pembelian');
        $id_detail_retur_pembelian = $this->input->post('id_detail_retur_pembelian');
        

        foreach($id_barang as $i => $item) { 

            $new_qty_retur[$i] = $qty_retur[$i] - $qty_ubah[$i];
            $new_qty_beli[$i] = $qty_beli[$i] + $qty_ubah[$i];

            $detail[] = array(
                'id_barang' => $id_barang[$i],
                'sku_barang' => $sku_barang[$i],
                'nama_barang' => $nama_barang[$i],
                'harga' => $harga[$i],
                'jumlah_beli' => $new_qty_beli[$i],
                'jumlah_retur' => $new_qty_retur[$i],
                'subtotal_retur' => $subtotal[$i],
                'id_detail_pembelian' => $id_detail_pembelian[$i],
                'id_detail_retur_pembelian' => $id_detail_retur_pembelian[$i]
             );

            $new_qty[$i] = $qty_retur[$i] + $qty_beli[$i];
            $new_subtotal[$i] = $new_qty_beli[$i]*$harga[$i];

            $beli_update[] = array(
                'id_detail_pembelian' => $id_detail_pembelian[$i],
                'jumlah' => $new_qty_beli[$i],
                'subtotal' => $new_subtotal[$i]
            );

            $currentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang[$i])->row();
            $newcurrent = $currentStok->stok;
            $newStok[$i] = $currentStok->stok + $qty_ubah[$i];

            $stok_barang[] = array(
                'id_barang' => $id_barang[$i],
                'stok' => $newStok[$i]
            );

            $history[] = array(
                'id_barang' => $id_barang[$i],
                'mod_stok' => $qty_retur[$i],
                'tanggal' => date('Y-m-d H:i:s'),
                'keterangan' => 'Batal retur pembelian barang',
                'id_penjualan' => 0,
                'id_pembelian' => $id_pembelian
             );
        }

         if($total_retur == 0){
            $this->db->update('tb_retur_pembelian',array('deleted' => '1'),array('id_retur_pembelian' => $id_retur_pembelian));
            $this->db->update('tb_detail_retur_pembelian',array('deleted'=>'1'),array('id_retur_pembelian' => $id_retur_pembelian));
        }else{
            $this->db->update('tb_retur_pembelian',array('deleted' => '0'),array('id_retur_pembelian' => $id_retur_pembelian));
            $this->db->update('tb_detail_retur_pembelian',array('deleted'=>'0'),array('id_retur_pembelian' => $id_retur_pembelian));
        }

        $this->db->update_batch('tb_detail_retur_pembelian',$detail,'id_detail_retur_pembelian');
        $this->db->update_batch('tb_detail_pembelian',$beli_update,'id_detail_pembelian');
        $this->db->update_batch('tb_barang', $stok_barang,'id_barang');
        $this->db->insert_batch('tb_history_stok', $history);
        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_pembelian WHERE id_pembelian='.$id_pembelian.' AND deleted=0')->result();
        foreach ($getTotal as $tot) {
            $total = $tot->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_pembelian WHERE id_pembelian='.$id_pembelian)->row();

        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
             $getFinalTotal = $total;
             $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_pembelian', array('nominal_ppn'=>$hitungppn,'total'=>$getFinalTotal),array('id_pembelian'=>$id_pembelian));


        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function cetak_penjualan_by_sales($id_sales)
    {
        return $this->db->select('tb_penjualan.*,tb_pelanggan.nama_pelanggan,tb_master_payment.nama_payment')
                        ->from('tb_penjualan')
                        ->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_penjualan.id_payment')
                        ->where('tb_penjualan.id_sales',$id_sales)
                        ->get()
                        ->result();
    }

    public function cetak_pembelian_by_supplier($id_supplier)
    {
        return $this->db->select('tb_pembelian.*,tb_master_payment.nama_payment')
                        ->from('tb_pembelian')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_pembelian.id_payment')
                        ->where('tb_pembelian.id_supplier',$id_supplier)
                        ->get()
                        ->result();
    }

    public function cetak_penjualan_by_pelanggan($id_pelanggan)
    {
        return $this->db->select('tb_penjualan.*,tb_pelanggan.nama_pelanggan,tb_master_payment.nama_payment,tb_sales.nama_sales')
                        ->from('tb_penjualan')
                        ->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_penjualan.id_payment')
                        ->join('tb_sales','tb_sales.id_sales=tb_penjualan.id_sales')
                        ->where('tb_penjualan.id_pelanggan',$id_pelanggan)
                        ->get()
                        ->result();
    }

    public function kartu_stok($id_barang)
    {
        return $this->db->select('tb_history_stok.*,tb_barang.id_barang,tb_barang.nama_barang,tb_barang.stok,tb_penjualan.nomor_penjualan,tb_penjualan.tanggal_penjualan,tb_pembelian.nomor_pembelian,tb_pembelian.tanggal_pembelian')
                        ->from('tb_history_stok')
                        ->join('tb_penjualan','tb_penjualan.id_penjualan=tb_history_stok.id_penjualan','left')
                        ->join('tb_pembelian','tb_pembelian.id_pembelian=tb_history_stok.id_pembelian','left')
                        ->join('tb_barang','tb_barang.id_barang=tb_history_stok.id_barang')
                        ->where('tb_barang.id_barang',$id_barang)
                        ->get()
                        ->result();
    }

    public function detail_harga_penjualan($id_barang, $id_pelanggan = 0)
    {
	    if($id_pelanggan > 0){
		    return $this->db->select('tb_detail_penjualan.*,tb_penjualan.tanggal_penjualan,tb_pelanggan.nama_pelanggan')
                        ->from('tb_detail_penjualan')
                        ->join('tb_penjualan','tb_penjualan.id_penjualan=tb_detail_penjualan.id_penjualan','left')
                        ->join('tb_pelanggan','tb_penjualan.id_pelanggan=tb_pelanggan.id_pelanggan','left')
                        ->limit(50)
                        ->order_by('id_detail_penjualan','DESC')
                        ->where('tb_detail_penjualan.deleted', 0)
                        ->where('tb_penjualan.deleted', 0)
                        ->where('tb_detail_penjualan.id_barang',$id_barang)
						->where('tb_penjualan.id_pelanggan', $id_pelanggan)
                        ->get()
                        ->result();
	    }
	    else{
	        return $this->db->select('tb_detail_penjualan.*,tb_penjualan.tanggal_penjualan,tb_pelanggan.nama_pelanggan')
                        ->from('tb_detail_penjualan')
                        ->join('tb_penjualan','tb_penjualan.id_penjualan=tb_detail_penjualan.id_penjualan','left')
                        ->join('tb_pelanggan','tb_penjualan.id_pelanggan=tb_pelanggan.id_pelanggan','left')
                        ->limit(50)
                        ->order_by('id_detail_penjualan','DESC')
                        ->where('tb_detail_penjualan.deleted', 0)
                        ->where('tb_penjualan.deleted', 0)
                        ->where('tb_detail_penjualan.id_barang',$id_barang)
                        ->get()
                        ->result();
        }
    }
    
    public function detail_harga_pembelian($id_barang, $id_supplier = 0)
    {
	    if($id_supplier > 0){
	        return $this->db->select('tb_detail_pembelian.*,tb_pembelian.tanggal_pembelian,tb_supplier.nama_supplier')
	                        ->from('tb_detail_pembelian')
	                        ->join('tb_pembelian','tb_pembelian.id_pembelian=tb_detail_pembelian.id_pembelian','left')
	                        ->join('tb_supplier','tb_pembelian.id_supplier=tb_supplier.id_supplier','left')
	                        ->limit(50)
	                        ->order_by('id_detail_pembelian','DESC')
	                        ->where('tb_detail_pembelian.deleted', 0)
							->where('tb_pembelian.deleted', 0)
	                        ->where('tb_detail_pembelian.id_barang',$id_barang)
	                        ->where('tb_pembelian.id_supplier', $id_supplier)
	                        ->get()
	                        ->result();
	    }
	    else{
		    return $this->db->select('tb_detail_pembelian.*,tb_pembelian.tanggal_pembelian,tb_supplier.nama_supplier')
	                        ->from('tb_detail_pembelian')
	                        ->join('tb_pembelian','tb_pembelian.id_pembelian=tb_detail_pembelian.id_pembelian','left')
	                        ->join('tb_supplier','tb_pembelian.id_supplier=tb_supplier.id_supplier','left')
	                        ->limit(50)
	                        ->order_by('id_detail_pembelian','DESC')
	                        ->where('tb_detail_pembelian.deleted', 0)
							->where('tb_pembelian.deleted', 0)
	                        ->where('tb_detail_pembelian.id_barang',$id_barang)
	                        ->get()
	                        ->result();
	    }
    }

    public function cetak_detail_penjualan($id_penjualan)
    {
        return $this->db->select('tb_penjualan.*,tb_sales.nama_sales,tb_master_payment.nama_payment,tb_master_payment.cash_payment,tb_user.username,tb_pelanggan.nama_pelanggan,
        						tb_pelanggan.alamat_pelanggan,tb_pelanggan.kota_pelanggan,tb_pelanggan.nomor_telepon_pelanggan,SUM(tb_retur_penjualan.total_retur) as total_retur_penjualan',FALSE)
                        ->from('tb_penjualan')
                        ->join('tb_sales','tb_sales.id_sales=tb_penjualan.id_sales')
                        ->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_penjualan.id_pelanggan')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_penjualan.id_payment')
                        ->join('tb_retur_penjualan','tb_retur_penjualan.id_penjualan=tb_penjualan.id_penjualan','left')
                        ->join('tb_user','tb_user.id_user=tb_penjualan.id_user')
                        ->where('tb_penjualan.id_penjualan',$id_penjualan)
                        ->get()
                        ->row();

    }

    public function cetak_detail_pembelian($id_pembelian)
    {
        return $this->db->select('tb_pembelian.*,tb_master_payment.nama_payment,tb_master_payment.cash_payment,tb_user.username,tb_supplier.nama_supplier,tb_supplier.alamat_supplier,tb_supplier.kota_supplier,tb_supplier.nomor_telepon_supplier,SUM(tb_retur_pembelian.total_retur_pembelian) as total_retur_pembelian',FALSE)
                        ->from('tb_pembelian')
                        ->join('tb_supplier','tb_supplier.id_supplier=tb_pembelian.id_supplier')
                        ->join('tb_master_payment','tb_master_payment.id_payment=tb_pembelian.id_payment')
                        ->join('tb_retur_pembelian','tb_retur_pembelian.id_pembelian=tb_pembelian.id_pembelian','left')
                        ->join('tb_user','tb_user.id_user=tb_pembelian.id_user')
                        ->where('tb_pembelian.id_pembelian',$id_pembelian)
                        ->get()
                        ->row();
    }

    public function add_stock_opname()
    {
        $id_barang = $this->input->post('id_barang');
        $sku_barang = $this->input->post('sku_barang');
        $nama_barang = $this->input->post('nama_barang');
        $stok = $this->input->post('stok');
        $stok_gudang = $this->input->post('stok_gudang');
        $catatan = $this->input->post('catatan');

        $stok_perubahan = $stok_gudang - $stok;

        $data = array(
            'id_barang' => $id_barang,
            'sku_barang' => $sku_barang,
            'nama_barang' => $nama_barang,
            'stok_database' => $stok,
            'stok_gudang' => $stok_gudang,
            'catatan' => $catatan
        );
        $date = date('d-m-Y H:i:s');

        $this->db->insert('tb_stock_opname', $data);
        
        $this->db->update('tb_barang',array('stok' => $stok_gudang),array('id_barang' => $id_barang));

        $history = array(
            'id_barang' => $id_barang,
            'mod_stok' => $stok_perubahan,
            'tanggal' => date('Y-m-d h:i:s'),
            'keterangan' => 'Mutasi barang '.$date,
            'id_penjualan' => 0,
            'id_pembelian' => 0
        );

        $this->db->insert('tb_history_stok', $history);
        
        if($this->db->affected_rows()){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function add_detail_stock_opname_baru_run($id_stock_opname_baru)
    {
        $id_barang = $this->input->post('id_barang');
        $sku_barang = $this->input->post('sku_barang');
        $nama_barang = $this->input->post('nama_barang');
        $stok = $this->input->post('stok');
        $stok_gudang = $this->input->post('stok_gudang');
        $catatan = $this->input->post('catatan');

        $stok_perubahan = $stok_gudang - $stok;

        $data = array(
            'id_barang' => $id_barang,
            'sku_barang' => $sku_barang,
            'nama_barang' => $nama_barang,
            'stok_database' => $stok,
            'stok_gudang' => $stok_gudang,
            'catatan' => $catatan,
            'id_stock_opname_baru' => $id_stock_opname_baru,
            'id_user' => $this->session->userdata('id_user')
        );
        $date = date('d-m-Y H:i:s');

        $this->db->insert('tb_detail_stock_opname_baru', $data);
        
        $this->db->update('tb_barang',array('stok' => $stok_gudang),array('id_barang' => $id_barang));

        $history = array(
            'id_barang' => $id_barang,
            'mod_stok' => $stok_perubahan,
            'tanggal' => date('Y-m-d h:i:s'),
            'keterangan' => 'Stok Opname '.$date,
            'id_penjualan' => 0,
            'id_pembelian' => 0
        );

        $this->db->insert('tb_history_stok', $history);
        
        if($this->db->affected_rows()){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function detail_stock_opname_baru($id_stock_opname_baru)
    {
        return $this->db->where('id_stock_opname_baru',$id_stock_opname_baru)
                        ->get('tb_stock_opname_baru')
                        ->row();
    }

    public function detail_daftar_stock_opname_baru($id_stock_opname_baru)
    {
        return $this->db->where('id_stock_opname_baru',$id_stock_opname_baru)
                        ->where('tb_detail_stock_opname_baru.deleted',0)
                        ->get('tb_detail_stock_opname_baru')
                        ->result();
    }

    public function get_all_sales()
    {
        return $this->db->select('*')
                        ->order_by('nama_sales','ASC')
                        ->where('deleted',0)
                        ->get('tb_sales')
                        ->result();
        
    }
    
    public function get_all_supplier()
    {
        return $this->db->select('*')
                        ->order_by('nama_supplier','ASC')
                        ->where('deleted',0)
                        ->get('tb_supplier')
                        ->result();
        
    }

    public function get_all_pelanggan()
    {
        return $this->db->select('*')
                        ->order_by('nama_pelanggan','ASC')
                        ->where('deleted',0)
                        ->get('tb_pelanggan')
                        ->result();
        
    }

    public function get_all_payment()
    {
        return $this->db->select('*')
                        ->order_by('id_payment','DESC')
                        ->where('deleted',0)
                        ->get('tb_master_payment')
                        ->result();
        
    }

    public function add_supplier()
    {
        $nama_supplier = $this->input->post('nama_supplier');
        $nama_perusahaan_supplier = $this->input->post('nama_perusahaan_supplier');
        $alamat_supplier = $this->input->post('alamat_supplier');
        $kota_supplier = $this->input->post('kota_supplier');
        $nomor_telepon_supplier = $this->input->post('nomor_telepon_supplier');
        $email_supplier = $this->input->post('email_supplier');

        $data = array(
            'nama_supplier' => $nama_supplier,
            'nama_perusahaan' => $nama_perusahaan_supplier,
            'alamat_supplier' => $alamat_supplier,
            'kota_supplier' => $kota_supplier,
            'nomor_telepon_supplier' => $nomor_telepon_supplier,
            'email_supplier' => $email_supplier 
        );

        $this->db->insert('tb_supplier', $data);

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function add_pelanggan()
    {
        $nama_pelanggan = $this->input->post('nama_pelanggan');
        $nama_perusahaan_pelanggan = $this->input->post('nama_perusahaan_pelanggan');
        $alamat_pelanggan = $this->input->post('alamat_pelanggan');
        $kota_pelanggan = $this->input->post('kota_pelanggan');
        $nomor_telepon_pelanggan = $this->input->post('nomor_telepon_pelanggan');
        $email_pelanggan = $this->input->post('email_pelanggan');

        $data = array(
            'nama_pelanggan' => $nama_pelanggan,
            'nama_perusahaan' => $nama_perusahaan_pelanggan,
            'alamat_pelanggan' => $alamat_pelanggan,
            'kota_pelanggan' => $kota_pelanggan,
            'nomor_telepon_pelanggan' => $nomor_telepon_pelanggan,
            'email_pelanggan' => $email_pelanggan 
        );

        $this->db->insert('tb_pelanggan', $data);

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function add_payment()
    {
       $nama_payment = $this->input->post('nama_payment');
       $cash_payment = $this->input->post('cash_payment');
       $status = $this->input->post('status');

       $data = array(
            'nama_payment' => $nama_payment,
            'cash_payment' => $cash_payment,
            'status' => $status 
        );

       $this->db->insert('tb_master_payment', $data);

       if($this->db->affected_rows()>0){
            return TRUE;
       }else{
            return FALSE;
       }
    }

    public function hapus_retur_penjualan($id_retur_penjualan)
    {
        $this->db->update('tb_retur_penjualan', array('deleted'=>'1'), array('id_retur_penjualan'=>$id_retur_penjualan));
        $getDetail = $this->db->query('SELECT * FROM tb_detail_retur_penjualan WHERE id_retur_penjualan='.$id_retur_penjualan.' AND deleted=0')->result();

        foreach ($getDetail as $data) {
            $id_barang = $data->id_barang;
            $jumlah_jual = $data->jumlah_jual;
            $jumlah_retur = $data->jumlah_retur;
            $harga = $data->harga;
            $id_detail_penjualan = $data->id_detail_penjualan;

            $this->db->update('tb_detail_retur_penjualan', array('deleted'=>'1'),array('id_retur_penjualan' => $id_retur_penjualan));

            $jumlah_balik = $jumlah_jual + $jumlah_retur;
            $new_subtotal = $harga * $jumlah_balik;

            //Reverse to detail penjualan
            $reverse = array(
                'jumlah' => $jumlah_balik,
                'subtotal' => $new_subtotal
            );
            $this->db->update('tb_detail_penjualan', $reverse,array('id_detail_penjualan'=>$id_detail_penjualan));

            //Pengurangan dari stok master barang
            $getCurrentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang)->row();
            $new_stok = $getCurrentStok->stok - $jumlah_retur;
            $this->db->update('tb_barang', array('stok'=> $new_stok),array('id_barang'=>$id_barang));

        }

        $generateTotalRetur = $this->db->query('SELECT SUM(subtotal_retur) as totalRetur FROM tb_detail_retur_penjualan WHERE id_retur_penjualan='.$id_retur_penjualan.' AND deleted=0')->result();
        foreach ($generateTotalRetur as $retur) {
            $totalRetur = $retur->totalRetur;
        }

        $this->db->update('tb_retur_penjualan', array('total_retur' => $totalRetur),array('id_retur_penjualan'=>$id_retur_penjualan));

        $getRowId = $this->db->query('SELECT * FROM tb_retur_penjualan WHERE id_retur_penjualan='.$id_retur_penjualan)->row();
        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_penjualan WHERE id_penjualan='.$getRowId->id_penjualan)->result();
        foreach ($getTotal as $row) {
            $total = $row->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_penjualan WHERE id_penjualan='.$getRowId->id_penjualan)->row();

        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
            $getFinalTotal = $total;
            $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_penjualan', array('nominal_ppn'=>$hitungppn,'total' => $getFinalTotal),array('id_penjualan'=>$getRowId->id_penjualan));
        // print_r($getFinalTotal);

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function hapus_retur_pembelian($id_retur_pembelian)
    {
        $this->db->update('tb_retur_pembelian', array('deleted'=>'1'), array('id_retur_pembelian'=>$id_retur_pembelian));
        $getDetail = $this->db->query('SELECT * FROM tb_detail_retur_pembelian WHERE id_retur_pembelian='.$id_retur_pembelian.' AND deleted=0')->result();

        foreach ($getDetail as $data) {
            $id_barang = $data->id_barang;
            $jumlah_beli = $data->jumlah_beli;
            $jumlah_retur = $data->jumlah_retur;
            $harga = $data->harga;
            $id_detail_pembelian = $data->id_detail_pembelian;

            $this->db->update('tb_detail_retur_pembelian', array('deleted'=>'1'),array('id_retur_pembelian' => $id_retur_pembelian));

            $jumlah_balik = $jumlah_beli + $jumlah_retur;
            $new_subtotal = $harga * $jumlah_balik;

            //Reverse to detail penjualan
            $reverse = array(
                'jumlah' => $jumlah_balik,
                'subtotal' => $new_subtotal
            );
            $this->db->update('tb_detail_pembelian', $reverse,array('id_detail_pembelian'=>$id_detail_pembelian));

            //Pengurangan dari stok master barang
            $getCurrentStok = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id_barang)->row();
            $new_stok = $getCurrentStok->stok + $jumlah_retur;
            $this->db->update('tb_barang', array('stok'=> $new_stok),array('id_barang'=>$id_barang));

        }

        $generateTotalRetur = $this->db->query('SELECT SUM(subtotal_retur) as totalRetur FROM tb_detail_retur_pembelian WHERE id_retur_pembelian='.$id_retur_pembelian.' AND deleted=0')->result();
        foreach ($generateTotalRetur as $retur) {
            $totalRetur = $retur->totalRetur;
        }

        $this->db->update('tb_retur_pembelian', array('total_retur_pembelian' => $totalRetur),array('id_retur_pembelian'=>$id_retur_pembelian));

        $getRowId = $this->db->query('SELECT * FROM tb_retur_pembelian WHERE id_retur_pembelian='.$id_retur_pembelian)->row();
        $getTotal = $this->db->query('SELECT SUM(subtotal) as totals FROM tb_detail_pembelian WHERE id_pembelian='.$getRowId->id_pembelian)->result();
        foreach ($getTotal as $row) {
            $total = $row->totals;
        }

        $getRow = $this->db->query('SELECT * FROM tb_pembelian WHERE id_pembelian='.$getRowId->id_pembelian)->row();

        if($getRow->ppn != 'Exclude'){
            $hitungppn = (10/100) * $total;
            $getFinalTotal = $total + $hitungppn;
        }else{
            $getFinalTotal = $total;
            $hitungppn = $getRow->nominal_ppn;
        }

        $this->db->update('tb_pembelian', array('nominal_ppn'=>$hitungppn,'total' => $getFinalTotal),array('id_pembelian'=>$getRowId->id_pembelian));
        // print_r($getFinalTotal);

        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}

/* End of file Transaction.php */
/* Location: ./application/models/Transaction.php */