<?php $this->load->view('head_dash'); ?>
<style type="text/css">
</style>
<!-- Form Penjualan -->
	<form method="post" id="formPenjualan">
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
            	<!-- <div class="card"> -->
            		<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        	<a href="<?php echo base_url();?>index.php/admin/penjualan" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
            	<!-- </div> -->
            		<br>
                        <div>
                            <!-- ROW 1 -->
                            <div class="card">
								<div class="row">
									<div class="col-md-3 col-sm-3">
										<div align="center" style="border-bottom: 1px lightgrey solid; margin-bottom: 10px;padding-top: 10px">
											<h3><img src="<?php echo base_url(); ?>assets/spada/images/icon.png" style="width: 30px;height: 30px;"> <b>PENJUALAN</b></h3>
										</div>
										<!-- FORM -->
										<div class="form-group">
											<label>Tanggal Penjualan</label>
											<?php
												date_default_timezone_set("Asia/Jakarta");
											echo'
											<input type="datetime" name="tanggal_penjualan" class="form-barang input-sm" placeholder="Tanggal Penjualan" value="'.date('Y-m-d H:i:s').'" readonly>';
											?>
										</div>
										<div class="form-group">
											<label>Pelanggan</label>
											<select class="form-control input-sm inputs" name="id_pelanggan" required="" id="id_pelanggan">
												<option value=""> Pilih Pelanggan</option>
												<?php
													if(!empty($pelanggan)){
														foreach ($pelanggan as $data) {
															if($data->deleted == 0){
																echo '<option value="'.$data->id_pelanggan.'">'.$data->nama_pelanggan.'</div>';
															}
														}
													}else{
														echo '<option value=""> Data Pelanggan kosong  </div>';
													}
												?>
											</select>
										</div>
										<div class="form-group">
											<label>Sales</label>
											<select class="form-control input-sm inputs" name="id_sales" required="" id="id_sales">
												<option value=""> Pilih Sales</option>
												<?php
													if(!empty($sales)){
														foreach ($sales as $data) {
															if($data->deleted == 0){
																echo '<option value="'.$data->id_sales.'">'.$data->nama_sales.'</div>';
															}
														}
													}else{
														echo '<option value=""> Data Sales kosong  </div>';
													}
												?>
											</select>
										</div>
										<div class="form-group">
											<label>Payment</label>
											<select class="form-control input-sm inputs" name="id_payment" required="" id="id_payment">
												<option value=""> Pilih Payment</option>
												<?php
													if(!empty($payment)){
														foreach ($payment as $data) {
															if($data->deleted == 0){
																if($data->status != 'Inactive'){
																	echo '<option value="'.$data->id_payment.'" data-cash="'.$data->cash_payment.'" data-type="'.$data->nama_payment.'">'.$data->nama_payment.'</div>';
																}
															}
														}
													}else{
														echo '<option value=""> Data Payment kosong  </div>';
													}
												?>
											</select>
											<input type="hidden" name="metode" id="metode" value="">
										</div>
										<div class="form-group">
											<label>PPN(10%)</label>
											<select class="form-control input-sm" name="ppn" id="ppn" readonly required="">
												<option value=""> Pilih PPN</option>
												<option value="Include" data-ppn="Include"> Include</option>
												<option value="Exclude" data-ppn="Exclude"> Exclude</option>
											</select>
										</div>
										<div class="form-group">
											<label>Status</label>
											<select class="form-control input-sm" name="status" id="status">
												<option value=""> Pilih Status</option>
												<option value="Belum Terbayar" data-ppn="Include" data-val="Belum Terbayar"> Belum Terbayar</option>
												<option value="Lunas" data-ppn="Exclude" data-val="Lunas"> Lunas</option>
												<option value="Batal" data-ppn="Exclude" data-val="Batal"> Batal</option>
											</select>
										</div>
										<div id="tanggal_lunas">
											<div class="form-group" >
												<label>Tanggal Lunas</label>
												<input type="text" name="tanggal_lunas"  class="form-control input-sm tgl_lunas" placeholder="Tanggal Lunas">
											</div>
										</div>
										<div id="tanggal_jatuh_tempo">
											<div class="form-group">
												<label>Jatuh Tempo</label>
												<input type="text" name="tanggal_jatuh_tempo" class="form-control input-sm jth_tmp tgl_lunas" id="tgl_tmp" placeholder="Tanggal Jatuh Tempo">
											</div>
										</div>
									</div>
									<div class="col-md-9 col-sm-9">

										<!-- TABEL BARANG -->
										<div class="form-group has-feedback has-search">
										<span class="glyphicon glyphicon-search form-control-feedback"></span>
								          <input name="search_data" class="form-control" id="search_data" placeholder="Search / Scan Produk" type="text" onkeyup="scan_data();">
								                <div id="suggestions">
								                 	<div id="autoSuggestionsList">
								                 	</div>
								            	</div>
								         	</div>
											<div class="scroll">
												<table class="table table-striped table-bordered" id="myTable">
													<thead>
														<tr>
															<th>SKU</th>
															<th>Produk</th>
															<th>Harga</th>
															<th>Cek</th>
															<th>Diskon (%)</th>
															<th>Qty</th>
															<th>Subtotal</th>
															<th>Aksi</th>
														</tr>
													</thead>
													<tbody id="row">

													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="col-md-4 col-sm-4">
													<div class="form-group" id="pay" style="display:none;">
														<label>BAYAR</label>
														<input type="text" name="bayar" class="form-control input-sm" id="bayar" readonly=""> 
													</div>
												</div>
												<div class="col-md-4 col-sm-4">
													<div class="form-group" id="back" style="display:none;">
														<label>KEMBALI</label>
														<input type="text" name="kembali"  class="form-control input-sm" id="kembali" readonly=""> 
													</div>
												</div>
												<div class="col-md-4 col-sm-4">
													<div class="form-group hide" id="show_nominal">
														<label>Nominal PPN</label>
														<input type="text" name="nominal_ppn" class="form-control input-sm" id="nominal_ppn" placeholder="Nominal PPN">
													</div>
													<div class="form-group">
														<label>TOTAL</label>
														<input type="text" name="total" class="form-control input-sm" id="total" readonly=""> 
													</div>
												</div>
											</div>
											<div class="giro">
												<br>
												<label>GIRO PAYMENT</label>
												<hr>
												<div class="row giro1">
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Nomor Giro</label>
															<input type="text" name="no_giro" class="form-control input-sm" placeholder="Nomor Giro">
														</div>
													</div>
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Nominal Giro</label>
															<input type="text" name="nominal_giro" id="nominal_giro" class="form-control input-sm" placeholder="Nominal Giro">
															<input type="checkbox" id="sama"><span class="red sama">*centang jika sama dengan total</span>
														</div>
													</div>
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Jatuh Tempo</label>
															<input type="text" name="tanggal_jatuh_tempo_giro" class="form-control input-sm tgl_lunas" placeholder="Tanggal Jatuh Tempo">
														</div>
													</div>
												</div>
												<div class="row giro2">
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Nama Bank</label>
															<input type="text" name="nama_bank" class="form-control input-sm" placeholder="Nama Bank">
														</div>
													</div>
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Keterangan</label>
															<input type="text" name="keterangan_giro" class="form-control input-sm" placeholder="Keterangan">
														</div>
													</div>
													<div class="col-sm-4 col-md-4">
														<div class="form-group">
															<label>Status</label>
															<select class="form-control input-sm" name="status_giro">
																<option value="Pending">Pending</option>
																<option value="Cair">Cair</option>
																<option value="Ditolak">Ditolak</option>
																<option value="Batal">Batal</option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div>
												<input type="submit" name="submit" class="btn btn-sm btn-success btn-act" id="save_jual" value="Simpan" onclick="return('Simpan Penjualan?');" style="width: 100%">
											</div>
										</div>
									</div>
		                        </div>
				            </div>
				        </div>
				    </div>
				</form>
<!-- END FORM PENJUALAN -->
	<!-- Modal Cek History Harga-->
	    <div class="modal fade" id="modalCek" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<h4 class="modal-title" id="exampleModalLabel">History Harga Barang</h4>
							</div>
							<div class="col-md-6 col-sm-6">
								<button type="button" class="btn btn-danger right btn-sm" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						</div>
	            	</div>
	            <div class="modal-body">
	                <table id="tableCek" class="table table-striped table-condensed table-bordered nowrap" style="width:100%">
	                    <thead>
	                        <tr>
	                          	<th>Tanggal</th>
	                          	<th>Pelanggan</th>
	                            <th>Harga</th>
	                            <th>Qty</th>
	                        </tr>
	                    </thead>
	                    <tbody id="detail_harga">
	                                       		
	                    </tbody>
	                </table> 
	            </div>
	            <div class="modal-footer">
	                
	            </div>  
	        </div>
	    </div>
	</div>
<!-- Modal Add Pelanggan -->
	<form method="post" id="formQuickAddBarang">
		<div class="modal fade" id="modalAddBarang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<h4 class="modal-title" id="exampleModalLabel">Tambah Barang</h4>
							</div>
							<div class="col-md-6 col-sm-6">
								<button type="button" class="btn btn-danger right btn-sm" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						</div>
		            </div>
		        <div class="modal-body">
		        	<div class="row">
		        		<div class="col-lg-6 col-md-6 col-sm-12">
		        			<div class="form-group">
				            	<label>SKU Barang</label>
				            	<input type="text" name="sku_barang" id="quick_sku_barang" class="form-control input-sm" placeholder="SKU barang">
				            </div>
				            <div class="form-group">
				            	<label>Nama Barang</label>
				            	<input type="text" name="nama_barang" id="quick_nama_barang" class="form-control input-sm" placeholder="Nama Barang">
				            </div>
				            <div class="form-group">
				            	<label>Satuan</label>
				            	<input type="text" name="satuan" id="quick_satuan" class="form-control input-sm" placeholder="Satuan">
				            </div>    
		        		</div>
		        		<div class="col-lg-6 col-md-6 col-sm-12">
		        			<div class="form-group">
				            	<label>Harga Modal</label>
				            	<input type="text" name="harga_modal" id="quick_harga_modal" class="form-control input-sm" placeholder="Harga Modal">
				            </div>
				            <div class="form-group">
				            	<label>Harga Jual</label>
				            	<input type="text" name="harga_jual" id="quick_harga_jual" class="form-control input-sm" placeholder="Harga Jual">
				            </div>
				            <div class="form-group">
				            	<label>Stok</label>
				            	<input type="text" name="stok_barang" id="quick_stok" class="form-control input-sm" placeholder="Stok">
				            </div>
		        		</div>
		        	</div>
		        </div>
			        <div class="modal-footer">
			        	<input type="submit" name="submit_quick_barang" class="btn btn-success btn-block btn-sm" value="Simpan Barang" id="btnQuickAddBarang">
			        </div>  
		        </div>
		    </div>
		</div>
	</form>
		<!-- Modal Add Barang -->
	<form method="post" id="formQuickAddPelanggan">
		<div class="modal fade" id="modalAddPelanggan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<h4 class="modal-title" id="exampleModalLabel">Tambah Pelanggan</h4>
							</div>
							<div class="col-md-6 col-sm-6">
								<button type="button" class="btn btn-danger right btn-sm" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						</div>
		            </div>
		        <div class="modal-body">
		        	<div class="row">
		        		<div class="col-lg-6 col-md-6 col-sm-12">
		        			<div class="form-group">
				            	<label>Nama Pelanggan</label>
				            	<input type="text" name="nama_pelanggan" id="quick_nama_pelanggan" class="form-control input-sm" placeholder="Nama Pelanggan">
				            </div>
				            <div class="form-group">
				            	<label>Nama Perusahaan</label>
				            	<input type="text" name="nama_perusahaan_pelanggan" id="quick_nama_perusahaan_pelanggan" class="form-control input-sm" placeholder="Nama Perusahaan">
				            </div>
				            <div class="form-group">
				            	<label>Alamat</label>
				            	<input type="text" name="alamat_pelanggan" id="quick_alamat_pelanggan" class="form-control input-sm" placeholder="Alamat Pelanggan">
				            </div>    
		        		</div>
		        		<div class="col-lg-6 col-md-6 col-sm-12">
		        			<div class="form-group">
				            	<label>Kota</label>
				            	<input type="text" name="kota_pelanggan" id="quick_kota_pelanggan" class="form-control input-sm" placeholder="Kota asal Pelanggan">
				            </div>
				            <div class="form-group">
				            	<label>Nomor Telepon</label>
				            	<input type="text" name="nomor_telepon_pelanggan" id="quick_nomor_telepon_pelanggan" class="form-control input-sm" placeholder="Nomor Telepon Pelanggan">
				            </div>
				            <div class="form-group">
				            	<label>Email</label>
				            	<input type="text" name="email_pelanggan" id="quick_email_pelanggan" class="form-control input-sm" placeholder="Email Pelanggan">
				            </div>
		        		</div>
		        	</div>
		        </div>
			        <div class="modal-footer">
			        	<input type="submit" name="submit_quick_pelanggan" class="btn btn-success btn-block btn-sm" value="Simpan Pelanggan" id="btnQuickAddPelanggan">
			        </div>  
		        </div>
		    </div>
		</div>
	</form>
<!-- Modal Add Payment -->
	<form method="post" id="formQuickAddPayment">
		<div class="modal fade" id="modalAddPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<h4 class="modal-title" id="exampleModalLabel">Tambah Payment</h4>
							</div>
							<div class="col-md-6 col-sm-6">
								<button type="button" class="btn btn-danger right btn-sm" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						</div>
		            </div>
		        <div class="modal-body">
		        	<div class="row">
		        		<div class="col-lg-12 col-md-12 col-sm-12">
		        			<div class="form-group">
				            	<label>Nama Payment</label>
				            	<input type="text" name="nama_payment" id="quick_nama_payment" class="form-control input-sm" placeholder="Nama Supplier">
				            </div>
				            <div class="form-group">
				            	<label>Cash Payment</label>
				            	<select name="cash_payment" id="quick_cash_payment" class="form-control input-sm">
				            		<option value=""> Pilih Cash Payment</option>
				            		<option>Yes</option>
				            		<option>No</option>
				            	</select>
				            </div>
				            <div class="form-group">
				            	<label>Status</label>
				            	<select name="status" id="quick_status" class="form-control input-sm">
				            		<option value=""> Pilih Status</option>
				            		<option>Active</option>
				            		<option>Inactive</option>
				            	</select>
				            </div>    
		        		</div>
		        	</div>
		        </div>
			        <div class="modal-footer">
			        	<input type="submit" name="submit_quick_payment" class="btn btn-success btn-block btn-sm" value="Simpan Payment" id="btnQuickAddPayment">
			        </div>  
		        </div>
		    </div>
		</div>
	</form>
		<!-- Floating Button Quick Add Supplier -->
			<div id="container-floating">
				<a href="#" data-toggle="modal" data-target="#modalAddPayment">
	                <div class="nd3 nds" data-toggle="tooltip" data-placement="left" data-original-title="Tambah Paymnet">
		               <img class="reminder">
		                    <p class="letter"><i class="fa fa-dollar-sign"></i></p>
		                </div>
	                </a>
				<a href="#" data-toggle="modal" data-target="#modalAddBarang">
	                <div class="nd4 nds" data-toggle="tooltip" data-placement="left" data-original-title="Tambah Barang">
		               <img class="reminder">
		                    <p class="letter"><i class="fa fa-archive"></i></p>
		                </div>
	                </a>
	            <a href="#" data-toggle="modal" data-target="#modalAddPelanggan">
	                <div class="nd1 nds" data-toggle="tooltip" data-placement="left" data-original-title="Tambah Pelanggan">
		               <img class="reminder">
		                    <p class="letter"><i class="fa fa-user"></i></p>
		                </div>
	                </a>
	                <div id="floating-button" data-toggle="tooltip" data-placement="left" data-original-title="Create">
	                    <p class="plus">+</p>
	                        <img class="edit" src="https://ssl.gstatic.com/bt/C3341AA7A1A076756462EE2E5CD71C11/1x/bt_compose2_1x.png">
	                </div>
	           </div>
<?php $this->load->view('foot_dash'); ?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-ui.js"></script>
<!--<script src="<?php echo base_url();?>assets/spada/js/transaksi.js"></script>-->
<script src="<?php echo base_url().auto_version('assets/spada/js/transaksi.js'); ?>"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
	$(".dropdown").click(function(e){
		if(e.target){
			$('.dropdown').addClass('open');
			$('.dropdown-toggle').attr('aria-expanded',true);
		}else{
			$('.dropdown').removeClass('open');
			$('.dropdown-toggle').attr('aria-expanded',false);
		}
	});
</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/spada/js/quick_add.js"></script>