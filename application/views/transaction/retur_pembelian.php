<?php $this->load->view('head_dash'); ?>
<form method="post" action="<?php echo base_url();?>index.php/transaction/simpan_retur_pembelian/<?php echo $this->uri->segment(3);?>">
	<div id="content-wrapper" class="group">
	    <div id="page-wrapper">
	    	<div class="row">
	    		<div class="col-md-6 col-sm-6">
	    			<div class="btn-group" role="group" aria-label="Basic example" style="width: 100%; display:none;">
					  <button type="button" class="btn btn-primary active">Tambah</button>
					  <button type="button" class="btn btn-primary">Ubah</button>
					</div>
	    		</div>
	    		<div class="col-md-6 col-sm-6">
	    			<a href="<?php echo base_url();?>index.php/admin/pembelian" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali</a>
	    		</div>
	    	</div>
	    	<br>
	    	<div class="card">
	    		<div class="row">
	    			<div class="col-md-12 col-sm-12">
	    				<em><h4 class="text-danger">* Halaman ini hanya untuk menambahkan retur barang</h4></em>
	    			</div>
	    		</div>
		        <div class="row">
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Nomor Pembelian</label>
		            		<input type="hidden" name="id_pembelian" value="<?php echo $this->uri->segment(3);?>">
		            		<input type="text" name="nomor_pembelian" class="form-barang input-sm" value="<?php echo $retur->nomor_pembelian;?>" readonly>
		            	</div>
		            	<div class="form-group">
		            		<label>Nomor Retur</label>
		            		<?php
		            			date_default_timezone_set("Asia/Jakarta");

							    $cek = $this->db->query("SELECT MAX(no_serial) as urut FROM tb_retur_pembelian ORDER BY id_retur_pembelian DESC LIMIT 1")->result_array();
							    foreach ($cek as $value) {
							        $ex = $value['urut'];
							            if (date('d')=='01'){ 
							                $urut = '1'; 
							            }
							            else{ 
							                $urut = $ex+1; 
							            }
							    }
							                    
							    $no_in = sprintf('%05s',$urut);
		            		echo '<input type="text" name="nomor_retur_pembelian" class="form-barang input-sm" value="RB-'.$no_in.'" readonly>
		            			<input type="hidden" name="no_serial" value="'.$urut.'">';
		            		?>
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Tanggal Pembelian</label>
		            		<input type="text" name="tanggal_pembelian" class="form-barang input-sm" value="<?php echo $retur->tanggal_pembelian;?>" readonly>
		            	</div>
		            	<div class="form-group">
		            		<label>Tanggal Retur</label>
		            			<div class="input-group">
								    <div class="input-group-addon">
								      <i class="fa fa-calendar"></i>
								    </div>
		            			<input type="text" name="tanggal_retur_pembelian" id="tgl_retur" placeholder="Tanggal Retur" class="form-control input-sm" required="">
		            		</div>
		            	</div>
		            </div>
		            <div class="col-lg-4 col-md-4 col-sm-4">
		            	<div class="form-group">
		            		<label>Supplier</label>
		            		<input type="text" name="id_supplier" class="form-barang input-sm" value="<?php echo $retur->nama_supplier;?>" readonly>
		            	</div>
		            </div>
		        </div>
		        <!-- Detail Pembelian -->
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<b><em><h5 class="text-danger">* Hapus barang apabila tidak diretur</h5></em></b>
		        		<div class="scroll">
							<table class="table table-striped table-bordered table-sm" id="myTable">
								<thead>
									<tr>
										<th>SKU</th>
										<th>Produk</th>
										<th>Harga</th>
										<th>Qty. Penjualan</th>
										<th>Dapat diretur (max)</th>
										<th>Qty. Retur</th>
										<th>Subtotal</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$query = $this->db->query('SELECT * FROM tb_detail_pembelian WHERE id_pembelian='.$retur->id_pembelian)->result();
									foreach ($query as $data) {
										if($data->subtotal > 0){
									echo'<tr>
						                <td>
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
						                	<input type="number" name="qty_beli[]" value="'.$data->jumlah.'" class="form-barang qtypenjualan input-sm" id="" required>
						                </td>
						                <td>
						                	<input type="number" name="bisadiretur" value="'.$data->jumlah.'" class="form-barang input-sm qtymax" required>
						                	<input type="hidden" name="diskon[]" value="'.$data->diskon.'">
						                </td>
						                <td>
						                	<input type="text" name="qty_retur[]" class="form-control qty quantityretur input-sm" onkeyup="update_qty()" required>
						                </td>
						                <td>
						                	<input type="text" name="subtotal_retur[]" class="form-barang subtotal input-sm" value="0" id="" required readonly>
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
		        		<textarea class="form-control" name="keterangan" style="height: 176px;" required=""></textarea>
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		
		        	</div>
		        	<div class="col-lg-4 col-md-4 col-sm-4">
		        		<div class="form-group">
		        			<label>Total Pembelian</label>
		        			<input type="text" name="total_pembelian" class="form-control input-sm" id="total_pembelian" value="<?php echo $retur->total;?>">
		        		</div>
		        		<div class="form-group">
		        			<label>Sisa Tagihan</label>
		        			<input type="text" name="sisa_tagihan" id="sisa_tagihan" class="form-control input-sm">
		        		</div>
		        		<div class="form-group">
		        			<label>Total Retur</label>
		        			<input type="text" name="total_retur_pembelian" id="total_retur" class="form-control input-sm">
		        		</div>
		        	</div>
		        </div>
		        <div class="row">
		        	<div class="col-md-12 col-sm-12">
		        		<input type="submit" name="submit" value="SIMPAN RETUR PEMBELIAN <?php echo $retur->nomor_pembelian;?>" class="btn btn-success btn-lg" id="btnSubmit" style="width: 100%">
		        	</div>
		        </div>
		    </div>
		   	<br>
		    <div class="card">
		    	<h3> Data Retur Pembelian <?php echo $retur->nomor_pembelian;?></h3>
		    	<hr>
		    	<div class="row">
					<div class="col-md-12 col-sm-12">
						<table id="tableRetur" class="table table-striped table-condensed table-bordered nowrap" style="width:100%">
				            <thead>
				                <tr>
				                    <th>Nomor Retur</th>
				                    <th>Tanggal Retur</th>
				                    <th>Keterangan</th>
				                    <th>Total Retur</th>
				                    <th>Detail</th>
				                </tr>
				            </thead>
				            <tbody>
				            	<?php
				            		foreach ($retured_items as $data) {
				            			if($data->deleted == 0){
				            			echo'
				            			<tr>
				            				<td>'.$data->no_retur_pembelian.'</td>
				            				<td>'.date('d F Y',strtotime($data->tanggal_retur_pembelian)).'</td>
				            				<td>'.$data->keterangan.'</td>
				            				<td> Rp '.number_format($data->total_retur_pembelian).'</td>
				            				<td>
				            					<a href="'.base_url().'index.php/admin/detail_retur_pembelian/'.$data->id_retur_pembelian.'" class="btn btn-default btn-sm"><i class="fa fa-file"></i> Detail Retur</a>
				            					<a href="'.base_url().'index.php/admin/retur_pembelian_print/'.$data->id_retur_pembelian.'" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak Retur</a>
				            					<a href="'.base_url().'index.php/admin/to_update_retur_pembelian/'.$data->id_retur_pembelian.'" class="btn btn-warning btn-sm"><i class="fa fa-sign-in-alt"></i> Batalkan Retur</a>
				            					<a href="'.base_url().'index.php/transaction/hapus_retur_pembelian/'.$data->id_retur_pembelian.'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus Retur</a>
				            				</td>
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
</form>
<?php $this->load->view('foot_dash'); ?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js
"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js
"></script>
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

   		function update_qty() {
            	update_amounts();
            	//sisa_tagihan();
            	total();
            	sisa_tagihan();
			    $('.quantityretur').keyup(function() {
			        update_amounts();
			        //sisa_tagihan();
			        total();
			        sisa_tagihan();
			    });
            }

        function sisa_tagihan() {
        	var total_pembelian = $("#total_pembelian").val();
        	var total_retur = $("#total_retur").val();
        	var sisa_tagihan = total_pembelian-total_retur;

        	$("#sisa_tagihan").val(sisa_tagihan);
        }

		function total() {
	            var sum = 0;

            $(".subtotal").each(function() {
                var value = $(this).val();
                
                if(!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });

            $("#total_retur").val(sum);
        	}

		function update_amounts(){
			    var sum = 0;
			    $('#myTable > tbody  > tr').each(function() {
			        var qty = $(this).find('.quantityretur').val();
			        var qtymax = $(this).find('.qtymax').val();
			        var price = $(this).find('.price').val();
			        var amount = (qty*price);
			        sum+=amount;
			        $(this).find('.subtotal').val(amount);

			        // if(qty > qtymax && event.keyCode != 46 && event.keyCode != 8){
			        // 	event.preventDefault();
			        // 	$(this).find('.quantityretur').val(qtymax);
			        // }
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