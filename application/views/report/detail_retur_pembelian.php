<?php $this->load->view('head_dash'); ?>
<!-- <form method="post" action="<?php echo base_url();?>index.php/transaction/simpan_retur_pembelian/<?php echo $this->uri->segment(3);?>"> -->
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
	    			$query = $this->db->query('SELECT id_pembelian FROM tb_retur_pembelian WHERE id_retur_pembelian='.$id)->row();
	    			echo'
	    			<a href="'.base_url().'index.php/admin/to_retur_pembelian/'.$query->id_pembelian.'" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali</a>';
	    			?>
	    		</div>
	    	</div>
	    	<br>
	    	<div class="card">
		        <div class="row">
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Nomor Pembelian</label>
		            		<input type="hidden" name="id_pembelian" value="<?php echo $this->uri->segment(3);?>">
		            		<input type="text" name="nomor_pembelian" class="form-barang input-sm" value="<?php echo $retur->nomor_pembelian;?>" disabled>
		            	</div>
		            	<div class="form-group">
		            		<label>Nomor Retur</label>
		            		<input type="text" name="nomor_retur_pembelian" class="form-barang input-sm" value="<?php echo $retur->no_retur_pembelian;?>" readonly>
		            		<input type="hidden" name="no_serial" value="<?php echo $retur->no_serial;?>">
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Tanggal Pembelian</label>
		            		<input type="text" name="tanggal_pembelian" class="form-barang input-sm" value="<?php echo $retur->tanggal_pembelian;?>" disabled>
		            	</div>
		            	<div class="form-group">
		            		<label>Tanggal Retur</label>
		            			<div class="input-group">
								    <div class="input-group-addon">
								      <i class="fa fa-calendar"></i>
								    </div>
		            			<input type="text" name="tanggal_retur_pembelian" id="tgl_retur" placeholder="Tanggal Retur" class="form-control input-sm" value="<?php echo $retur->tanggal_retur_pembelian;?>" disabled>
		            		</div>
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Supplier</label>
		            		<input type="text" name="id_supplier" class="form-barang input-sm" value="<?php echo $retur->nama_supplier;?>" disabled>
		            	</div>
		            </div>
		        </div>
		        <!-- Detail Pembelian -->
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<div class="scroll">
							<table class="table table-striped table-bordered table-sm" id="myTable">
								<thead>
									<tr>
										<th>SKU</th>
										<th>Produk</th>
										<th>Harga</th>
										<th>Qty. Pembelian</th>
										<th>Qty. Retur</th>
										<th>Subtotal</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$id = $this->uri->segment(3);
									$query = $this->db->query('SELECT * FROM tb_detail_retur_pembelian WHERE id_retur_pembelian='.$id)->result();
									foreach ($query as $data) {
										if($data->subtotal_retur > 0){
									echo'<tr>
						                <td>
						                	<input type="hidden" name="id_detail_pembelian[]" value="'.$data->id_detail_pembelian.'">
						                	<input type="hidden" name="id_barang[]" value="'.$data->id_barang.'">
						                	<input type="text" name="sku_barang[]" value="'.$data->sku_barang.'" class="form-barang input-sm" disabled>
						                </td>
						                <td>
						                	<input type="text" name="nama_barang[]" value="'.$data->nama_barang.'" class="form-barang input-sm" required readonly>
						                </td>
						                <td>
						                	<input type="text" name="harga_barang[]" value="'.number_format($data->harga).'" class="form-barang price input-sm" disabled>
						                </td>
						                <td>
						                	<input type="number" name="qty_beli[]" value="'.$data->jumlah_beli.'" class="form-barang qtypenjualan input-sm" disabled>
						                </td>
						                <td>
						                	<input type="number" name="qty_retur[]" class="form-barang qty quantityretur input-sm" onkeyup="update_qty()" value="'.$data->jumlah_retur.'" disabled>
						                </td>
						                <td>
						                	<input type="text" name="subtotal_retur[]" class="form-barang subtotal input-sm" value="'.number_format($data->subtotal_retur).'" disabled>
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
		        		<textarea class="form-control" name="keterangan" style="height: 176px;" disabled=""><?php echo $retur->keterangan;?></textarea>
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		<div class="form-group">
		        			<label>Total Pembelian</label>
		        			<input type="text" name="total_pembelian" class="form-barang input-sm" id="total_pembelian" value="<?php echo number_format($retur->total);?>">
		        		</div>
		        		<div class="form-group">
		        			<label>Sisa Tagihan</label>
		        			<?php
		        			$total_beli = $retur->total;
		        			$total_retur = $retur->total_retur_pembelian;
		        			$sisa_tagihan = $total_beli - $total_retur;
		        			echo'
		        			<input type="text" name="sisa_tagihan" id="sisa_tagihan" value="'.number_format($sisa_tagihan).'" class="form-barang input-sm">';
		        			?>
		        		</div>
		        		<div class="form-group">
		        			<label>Total Retur</label>
		        			<input type="text" name="total_retur_pembelian" id="total_retur" class="form-barang input-sm" value="<?php echo number_format($retur->total_retur_pembelian);?>">
		        		</div>
		        	</div>
		        </div>
		        <div class="row">
		        	<!-- <div class="col-md-12">
		        		<input type="submit" name="submit" value="SIMPAN RETUR PEMBELIAN <?php echo $retur->nomor_pembelian;?>" class="btn btn-success btn-lg" id="btnSubmit" style="width: 100%">
		        	</div> -->
		        </div>
		    </div>
	    </div>
	</div>
</form>
<?php $this->load->view('foot_dash'); ?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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