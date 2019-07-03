<?php $this->load->view('head_dash'); ?>
<form method="post" action="<?php echo base_url();?>index.php/transaction/update_retur_pembelian/<?php echo $this->uri->segment(3);?>">
	<div id="content-wrapper" class="group">
	    <div id="page-wrapper">
	    	<div class="row">
	    		<div class="col-md-6 col-sm-6">
	    			<div class="btn-group" role="group" aria-label="Basic example" style="width: 100%">
					  <button type="button" class="btn btn-primary">Tambah</button>
					  <button type="button" class="btn btn-primary active">Ubah</button>
					</div>
	    		</div>
	    		<div class="col-md-6 col-sm-6">
	    			<a href="<?php echo base_url();?>index.php/admin/to_retur_pembelian/<?php echo $update->id_pembelian;?>" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali</a>
	    		</div>
	    	</div>
	    	<br>
	    	<div class="card">
	    		<div class="row">
	    			<div class="col-md-12 col-sm-12">
	    				<em><h4 class="text-danger">* Halaman ini hanya untuk membatalkan/mengurangi jumlah retur barang</h4></em>
	    			</div>
	    		</div>
		        <div class="row">
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Nomor Pembelian</label>
		            		<input type="hidden" name="id_pembelian" value="<?php echo $update->id_pembelian;?>">
		            		<input type="text" name="nomor_pembelian" class="form-barang input-sm" value="<?php echo $update->nomor_pembelian;?>" readonly>
		            	</div>
		            	<div class="form-group">
		            		<label>Nomor Retur</label>
		            		<input type="text" name="nomor_retur_pembelian" class="form-barang input-sm" value="<?php echo $update->no_retur_pembelian;?>" readonly>
		            		<input type="hidden" name="no_serial" value="<?php echo $update->no_serial;?>">
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Tanggal Pembelian</label>
		            		<input type="text" name="tanggal_pembelian" class="form-barang input-sm" value="<?php echo $update->tanggal_pembelian;?>" readonly>
		            	</div>
		            	<div class="form-group">
		            		<label>Tanggal Retur</label>
		            			<div class="input-group">
								    <div class="input-group-addon">
								      <i class="fa fa-calendar"></i>
								    </div>
		            			<input type="text" name="tanggal_retur_pembelian" id="tgl_retur" placeholder="Tanggal Retur" class="form-control input-sm" value="<?php echo $update->tanggal_retur_pembelian;?>" required="">
		            		</div>
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Supplier</label>
		            		<input type="text" name="id_supplier" class="form-barang input-sm" value="<?php echo $update->nama_supplier;?>" readonly>
		            	</div>
		            </div>
		        </div>
		        <!-- Detail Pembelian -->
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<em><h5 class="text-danger">* Hapus apabila barang apabila tidak dikembalikan retur</h5></em>
		        		<div class="scroll">
							<table class="table table-striped table-bordered table-sm" id="myTable">
								<thead>
									<tr>
										<th>SKU</th>
										<th>Produk</th>
										<th>Harga</th>
										<th>Qty. Beli</th>
										<th>Qty. Retur</th>
										<th>Qty. Ubah</th>
										<th>Subtotal</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$query = $this->db->query('SELECT * FROM tb_detail_retur_pembelian WHERE id_retur_pembelian='.$update->id_retur_pembelian)->result();
									foreach ($query as $data) {
										if($data->subtotal_retur > 0){
									echo'<tr>
						                <td>
						                	<input type="hidden" name="id_detail_retur_pembelian[]" value="'.$data->id_detail_retur_pembelian.'">
						                	<input type="hidden" name="id_detail_pembelian[]" value="'.$data->id_detail_pembelian.'">
						                	<input type="hidden" name="id_barang[]" value="'.$data->id_barang.'">
						                	<input type="text" name="sku_barang[]" value="'.$data->sku_barang.'" class="form-barang input-sm" required readonly>
						                </td>
						                <td>
						                	<input type="text" name="nama_barang[]" value="'.$data->nama_barang.'" class="form-barang input-sm" required readonly>
						                </td>
						                <td>
						                	<input type="text" name="harga_barang[]" value="'.$data->harga.'" class="form-barang price input-sm" required readonly>
						                </td>
						                <td>
						                	<input type="number" name="qty_beli[]" value="'.$data->jumlah_beli.'" class="form-barang qtybeli input-sm" required>
						                </td>
						                <td>
						                	<input type="number" name="qty_retur[]" value="'.$data->jumlah_retur.'" class="form-barang qtyretur input-sm" required>
						                </td>
						                <td>
						                	<input type="text" name="qty_ubah[]" class="form-control qtyubah input-sm" onkeyup="update_qty()" required>
						                </td>
						                <td>
						                	<input type="text" name="subtotal_retur[]" value="'.$data->subtotal_retur.'" class="form-barang subtotal input-sm" required readonly>
						                </td>
						                <td>
						                	<button type="button" class="btn btn-danger btn-sm del" onclick="hapus_row(this);"><i class="fa fa-trash"></i></button>
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
		        		<textarea class="form-control" name="keterangan" style="height: 176px;" required=""><?php echo $update->keterangan;?></textarea>
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		<div class="form-group">
		        			<label>Total Pembelian</label>
		        			<input type="text" name="total_pembelian" class="form-control input-sm" id="total_pembelian" value="<?php echo $update->total;?>">
		        		</div>
		        		<div class="form-group">
		        			<label>Sisa Tagihan</label>
		        			<input type="text" name="sisa_tagihan" id="sisa_tagihan" class="form-control input-sm" value="<?php echo $update->sisa_tagihan;?>">
		        		</div>
		        		<div class="form-group">
		        			<label>Total Retur</label>
		        			<input type="text" name="total_retur_pembelian" id="total_update_retur" class="form-control input-sm">
		        		</div>
		        	</div>
		        </div>
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<input type="submit" name="submit" value="UPDATE RETUR PEMBELIAN <?php echo $update->nomor_pembelian;?>" class="btn btn-info btn-lg" id="btnSubmit" style="width: 100%">
		        	</div>
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
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script>
   $.noConflict();
        jQuery(document).ready(function ($) {
            $("#tgl_retur").datepicker({dateFormat: 'yy-mm-dd'});
            $('#tableRetur').DataTable({
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

   		function update_qty() {
            	total();
            	update_amounts();
			    $('.qtyubah').keyup(function() {
			        update_amounts();
			        total();
			    });
            }

		function total() {
	        var sum = 0;

            $(".subtotal").each(function() {
                var value = $(this).val();
                
                if(!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });

            $("#total_update_retur").attr('value',sum);
        }

		function update_amounts(){
			    var sum = 0;
			    $('#myTable > tbody  > tr').each(function() {
			    	var qtyubah = $(this).find('.qtyubah').val();
			        var qtyretur = $(this).find('.qtyretur').val();
			        var qtybeli = $(this).find('.qtybeli').val();
			        var qty = $(this).find('.quantityretur').val();
			        var price = $(this).find('.price').val();
			        var new_qty = qtyretur-qtyubah;
			        var amount = (new_qty*price);
			        sum+=amount;
			        $(this).find('.subtotal').attr('value',amount);

			        if(qtyubah > qtyretur && event.keyCode != 46 && event.keyCode != 8){
			        	event.preventDefault();
			        	$(this).find('.qtyubah').val(qtyretur);
			        }
			    });
			}
		
	$(".dropdown").click(function(e){
		if(e.target){
			$('.dropdown').addClass('open');
			$('.dropdown-toggle').attr('aria-expanded',true);
		}else{
			$('.dropdown').removeClass('open');
			$('.dropdown-toggle').attr('aria-expanded',false);
		}
	});

	function hapus_row(e) {
            	$(e).parent().parent().remove();
            	// total();
            	// ppn();
            }
  </script>