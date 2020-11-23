<?php $this->load->view('head_dash');?>
<div id="content-wrapper" class="group">
	<div id="page-wrapper">
		<div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <a href="<?php echo base_url();?>index.php/admin/barang" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                <br>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="card">
					<?php
					$id = $this->uri->segment(3);
					$query = $this->db->query('SELECT * FROM tb_barang WHERE id_barang='.$id)->row();
				   	echo '<h3> Kartu Stock '.$query->nama_barang.' </h3>
				   		<table width="30%">
					   		<tr>
					   			<th>SKU Barang</th>
					   			<td>:</td>
					   			<td>'.$query->sku_barang.'</td>
					   		</tr>
					   		<tr>
					   			<th>Harga Modal</th>
					   			<td>:</td>
					   			<td>Rp '.number_format($query->harga_modal).'</td>
					   		</tr>
					   		<tr>
					   			<th>Harga Jual</th>
					   			<td>:</td>
					   			<td>Rp '.number_format($query->harga_jual).'</td>
					   		</tr>
					   		<tr>
					   			<th>Masuk Tanggal</th>
					   			<td>:</td>
					   			<td>'.date('Y-m-d',strtotime($query->created)).'</td>
					   		</tr>
					   		<tr>
					   			<th>Stok Saat Ini</th>
					   			<td>:</td>
					   			<td>'.$query->stok.'</td>
					   		</tr>
				   		</table>';

				   	?>
				    	<hr>
				    	<div class="row">
							<div class="col-md-12 col-sm-12">
								<table id="tableKartu" class="table table-striped table-bordered table-condensed nowrap" style="width:100%">
						            <thead>
						                <tr>
						                	<th>No. </th>
						                    <th>Keterangan</th>
						                    <th>No. Transaksi</th>
						                    <th>Tanggal</th>
						                    <th>Stok Masuk</th>
						                    <th>Stok Keluar</th>
						                </tr>
						            </thead>
						            <tbody>
						            	<?php
						            	$row = 0;
						            	$no = 1;
						            		foreach ($kartu_stok as $data) {
						            			if($data->deleted == 0){
						            				if($data->keterangan == 'Penjualan' || $data->keterangan == 'Update Detail Penjualan'){
						            					$new = $data->nomor_penjualan;
						            					$tgl = $data->tanggal_penjualan;
						            				}else if($data->keterangan == 'Pembelian' || $data->keterangan == 'Update Detail Pembelian'){
						            					$new = $data->nomor_pembelian;
						            					$tgl = $data->tanggal_pembelian;
						            				}else{
						            					$new = '-';
						            					$tgl = $data->tanggal;
						            				}

						            				if($data->mod_stok < 0){
						            					$keluar = $data->mod_stok;
						            					$masuk = 0;
						            				}else{
						            					$masuk = $data->mod_stok;
						            					$keluar = 0;
						            				}
						            			echo'
						            			<tr>
						            				<td>'.$no++.'</td>
						            				<td>'.$data->keterangan.'</td>
						            				<td>'.$new.'</td>						            				
						            				<td>'.$tgl.'</td>
						            				<td>'.$masuk.'</td>
						            				<td>'.$keluar.'</td>
						            			</tr>
						            			';
						            			}
						            		}
						            	?>
						            </tbody>
								</table>
							</div>
						</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/DataTables/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
<script src="<?php echo base_url();?>assets/DataTables/Bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/DataTables/datatables.min.js"></script>
<script>
   $.noConflict();
        jQuery(document).ready(function ($) {
            // $("#tgl_retur").datepicker({dateFormat: 'yy-mm-dd'});
            $('#tableKartu').DataTable({
            		dom: 'Bfrtip',
			        buttons: [
			            'copy', 'csv', 'excel', 'pdf', 'print'
			        ],
	              // "lengthMenu": [[5, 10, 50, -1], [5, 10, 25, "All"]],
	              // "sPaginationType": "full_numbers"
					
	            });
        });
    </script>