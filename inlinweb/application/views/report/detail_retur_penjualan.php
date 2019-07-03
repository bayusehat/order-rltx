<?php $this->load->view('head_dash'); ?>
<!-- <form method="post" action="<?php echo base_url();?>index.php/transaction/simpan_retur_penjualan/<?php echo $this->uri->segment(3);?>"> -->
	<div id="content-wrapper" class="group">
	    <div id="page-wrapper">
	    	<div class="row">
	    		<div class="col-md-6 col-sm-6">
	    			<!-- <div class="btn-group" role="group" aria-label="Basic example" style="width: 100%">
					  <button type="button" class="btn btn-primary active">Tambah</button>
					  <button type="button" class="btn btn-primary">Ubah</button>
					</div> -->
	    		</div>
	    		<div class="col-md-6 col-sm-6">
	    			<?php
	    			$id = $this->uri->segment(3);
	    			$query = $this->db->query('SELECT id_penjualan FROM tb_retur_penjualan WHERE id_retur_penjualan='.$id)->row();
	    			$id_penjualan = $query->id_penjualan;
	    			echo '<a href="'.base_url().'index.php/admin/to_retur_penjualan/'.$id_penjualan.'" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali</a>';
	    			?>
	    		</div>
	    	</div>
	    	<br>
	    	<div class="card">
		        <div class="row">
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Nomor Penjualan</label>
		            		<input type="hidden" name="id_penjualan" value="<?php echo $this->uri->segment(3); ?>" disable>
		            		<input type="text" name="nomor_penjualan" class="form-barang input-sm" value="<?php echo $retur->nomor_penjualan;?>" disable>
		            	</div>
		            	<div class="form-group">
		            		<label>Nomor Retur</label>
							<input type="text" name="nomor_retur_penjualan" class="form-barang input-sm" value="<?php echo $retur->nomor_retur_penjualan;?>" readonly>
							<input type="hidden" name="no_serial" value="<?php echo $retur->no_serial;?>">
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Tanggal Penjualan</label>
		            		<input type="text" name="tanggal_penjualan" class="form-barang input-sm" value="<?php echo $retur->tanggal_penjualan;?>" disabled>
		            	</div>
		            	<div class="form-group">
		            		<label>Tanggal Retur</label>
			            		<div class="input-group">
								    <div class="input-group-addon">
								      <i class="fa fa-calendar"></i>
								    </div>
				            	<input type="text" name="tanggal_retur_penjualan" class="form-barang input-sm" id="tgl_retur" placeholder="Tanggal Retur" value="<?php echo $retur->tanggal_retur_penjualan;?>" disabled>
		            		</div>
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Pelanggan</label>
		            		<input type="text" name="id_pelanggan" class="form-barang input-sm" value="<?php echo $retur->nama_pelanggan;?>" disabled>
		            	</div>
		            </div>
		        </div>
		        <!-- Detail Penjualan -->
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<div class="scroll">
							<table class="table table-striped table-bordered table-condensed" id="myTable">
								<thead>
									<tr>
										<th>SKU</th>
										<th>Produk</th>
										<th>Harga</th>
										<th>Qty. Penjualan</th>
										<th>Qty. Retur</th>
										<th>Subtotal</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$id = $this->uri->segment(3);
									$query = $this->db->query('SELECT * FROM tb_detail_retur_penjualan WHERE id_retur_penjualan='.$id)->result();
									foreach ($query as $data) {
										if($data->subtotal_retur > 0){
											echo'
												<tr>
									                <td>
									                	<input type="hidden" name="id_detail_penjualan[]" value="'.$data->id_detail_penjualan.'">
									                	<input type="hidden" name="id_barang[]" value="'.$data->id_barang.'">
									                	<input type="text" name="sku_barang[]" value="'.$data->sku_barang.'" class="form-barang input-sm" disabled>
									                </td>
									                <td>
									                	<input type="text" name="nama_barang[]" value="'.$data->nama_barang.'" class="form-barang input-sm" disabled>
									                </td>
									                <td>
									                	<input type="text" name="harga_barang[]" value="'.number_format($data->harga).'" class="form-barang price input-sm" disabled>
									                </td>
									                <td>
									                	<input type="number" name="qty_jual[]" value="'.$data->jumlah_jual.'" class="form-barang qtypenjualan input-sm" disabled>
									                </td>
									                <td>
									                	<input type="number" name="qty_retur[]" class="form-barang quantityretur input-sm" onkeyup="update_qty()" value="'.$data->jumlah_retur.'" disabled>
									                </td>
									                <td>
									                	<input type="text" name="subtotal_retur[]" value="'.number_format($data->subtotal_retur).'" class="form-barang subtotal input-sm" disabled>
									                </td>
									               
									            </tr>';
									        }else{
									        	echo '';
									        }
							        	}
							        ?>
								</tbody>
							</table>
						</div>
		        	</div>
		        </div>
		        <div class="row">
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		<label> Keterangan</label>
		        		<textarea class="form-barang" name="keterangan" style="height: 176px;" disabled><?php echo $retur->keterangan;?></textarea>
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		<div class="form-group">
		        			<label>Total Penjualan</label>
		        			<input type="text" name="total_penjualan" id="total_penjualan" class="form-barang input-sm" value="<?php echo $retur->total;?>" disabled>
		        		</div>
		        		<div class="form-group">
		        			<label>Sisa Tagihan</label>
		        			<?php
		        			$total_jual= $retur->total;
		        			$total_retur = $retur->total_retur;

		        			$sisa_tagihan = $total_jual-$total_retur;
		        			echo'
		        			<input type="text" name="sisa_tagihan" id="sisa_tagihan" class="form-control input-sm" value="'.$sisa_tagihan.'" disabled>';
		        			?>
		        		</div>
		        		<div class="form-group">
		        			<label>Total Retur</label>
		        			<input type="text" name="total_retur" class="form-control input-sm" id="total_retur" value="<?php echo $retur->total_retur;?>" disabled>
		        		</div>
		        	</div>
		        </div>
		        <div class="row">
		        	<!-- <div class="col-md-12">
		        		<input type="submit" name="submit" value="SIMPAN RETUR PENJUALAN <?php echo $retur->nomor_penjualan;?>" class="btn btn-success btn-lg" id="btnSubmit" style="width: 100%">
		        	</div> -->
		        </div>
		    </div>
	    </div>
	</div>
<!-- </form> -->
<?php $this->load->view('foot_dash'); ?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
   $.noConflict();
        jQuery(document).ready(function ($) {
            $("#tgl_retur").datepicker({dateFormat: 'yy-mm-dd'});
            $('#tableRetur').DataTable({
            		dom: 'Bfrtip',
			        buttons: [
			            'copy', 'csv', 'excel', 'pdf', 'print'
			        ],
	              "lengthMenu": [[5, 10, 50, -1], [5, 10, 25, "All"]]
	            });

            $("#btnSubmit").click(function(){
            	var tbody = $("#myTable tbody");

				if(tbody.children().length == 0){
					alert('Tabel tidak boleh kosong');
					window.location.reload();
				}else{
					
				}
            })
        });
  </script>