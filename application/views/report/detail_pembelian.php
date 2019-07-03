<?php $this->load->view('head_dash'); ?>
<!-- Form Penjualan -->
	<!-- <form method="post"> -->
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
            	<!-- <div class="card"> -->
            		<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        	<a href="<?php echo base_url();?>index.php/admin/pembelian" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
            	<!-- </div> -->
            		<br>
                        <div>
                            <!-- ROW 1 -->
                            <form method="post" action="<?php echo base_url();?>index.php/transaction/edit_pembelian/<?php echo $this->uri->segment(3);?>">
                            <div class="card">
								<div class="row">
									<div class="col-md-3 col-sm-3">
										<div align="center" style="border-bottom: 1px lightgrey solid; margin-bottom: 10px;padding-top: 10px">
											<h3><img src="<?php echo base_url(); ?>assets/spada/images/icon.png" style="width: 30px;height: 30px;"> <b>PEMBELIAN</b></h3>
										</div>
										<!-- FORM -->
										<div class="form-group">
											<label>Nomor Pembelian</label>
											<input type="text" name="nomor_pembelian" class="form-barang input-sm" placeholder="Nomor Pembelian" value="<?php echo $detail->nomor_pembelian;?>">
											<input type="hidden" name="no_serial" value="<?php echo $detail->no_serial;?>">
										</div>
										<div class="form-group">
											<label>Tanggal Pembelian</label>
											<input type="datetime" name="tanggal_pembelian" class="form-barang input-sm" placeholder="Tanggal Pembelian" value="<?php echo date('Y-m-d H:i:s',strtotime($detail->tanggal_pembelian));?>">
										</div>
										<div id="no_nota_supplier">
											<div class="form-group" >
												<label>No. Nota Supplier</label>
												<input type="text" name="no_nota_supplier"  class="form-control input-sm no_nota_supplier" placeholder="No. Nota Supplier" 
												value="<?php echo $detail->no_nota_supplier; ?>">
											</div>
										</div>
										<div class="form-group">
											<label>Supplier</label>
											<select class="form-control input-sm inputs" name="id_supplier" required="" id="id_supplier">
												<option value="<?php echo $detail->id_supplier;?>"> <?php echo $detail->nama_supplier;?></option>
												<?php
												$id = $detail->id_supplier;
													if(!empty($supplier)){
														foreach ($supplier as $data) {
															if($data->deleted == 0 && $data->id_supplier != $id){
																echo '<option value="'.$data->id_supplier.'">'.$data->nama_supplier.'</div>';
															}
														}
													}else{
														echo '<option value=""> Data Supplier kosong  </div>';
													}
												?>
											</select>
										</div>
										<div class="form-group">
											<label>Payment</label>
											<select class="form-control input-sm inputs" name="id_payment" required="" id="id_payment">
												<option value="<?php echo $detail->id_payment;?>" data-cash="<?php echo $detail->cash_payment;?>'" data-type="<?php echo $detail->nama_payment;?>"> <?php echo $detail->nama_payment;?></option>
												<?php
												$id = $detail->id_payment;
													if(!empty($payment)){
														foreach ($payment as $data) {
															if($data->deleted == 0 && $data->id_payment != $id){
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
											<input type="hidden" name="metode" id="metode">
										</div>
										<div class="form-group">
											<label>PPN(10%)</label>
											<select class="form-control input-sm" name="ppn" id="ppn" required="" disabled>
												<option value="<?php echo $detail->ppn;?>"> <?php echo $detail->ppn;?></option>
												<?php
													$ppn = $detail->ppn;
													if($ppn == 'Include'){
														echo '<option value="Exclude">Exclude</option>';
													}else{
														echo '<option value="Include">Include</option>';
													}
												?>
											</select>
										</div>
										<div class="form-group">
											<label>Status</label>
											<select class="form-control input-sm" name="status" id="status">
												<option value="<?php echo $detail->status;?>"> <?php echo $detail->status;?></option>
												<?php
													$status = $detail->status;

													if($status == 'Belum Terbayar'){
														echo '<option value="Lunas" data-ppn="Exclude" data-val="Lunas"> Lunas</option>
															  <option value="Batal" data-ppn="Exclude" data-val="Batal"> Batal</option>';
													}else if($status == 'Lunas'){
														echo '<option value="Belum Terbayar" data-ppn="Include" data-val="Belum Terbayar"> Belum Terbayar</option>
																<option value="Batal" data-ppn="Exclude" data-val="Batal"> Batal</option>';
													}else{
														echo '<option value="Belum Terbayar" data-ppn="Include" data-val="Belum Terbayar"> Belum Terbayar</option>
															  <option value="Lunas" data-ppn="Exclude" data-val="Lunas"> Lunas</option>';
													}
												?>
											</select>
										</div>
										<div id="tanggal_lunas" <?php if($status != "Lunas") echo "style='display:none;'"; ?>>
											<div class="form-group">
												<label>Tanggal Lunas</label>
												<input type="text" name="tanggal_lunas" value="<?php echo $detail->tanggal_lunas;?>" class="form-control input-sm tgl_lunas" placeholder="Tanggal Lunas">
											</div>
										</div>
										<div id="tanggal_jatuh_tempo" <?php if($status != "Belum Terbayar") echo "style='display:none;'"; ?>>
											<div class="form-group">
												<label>Jatuh Tempo</label>
												<input type="text" name="tanggal_jatuh_tempo" class="form-control input-sm jth_tmp tgl_lunas" id="tgl_tmp" value="<?php echo $detail->tanggal_jatuh_tempo;?>" placeholder="Tanggal Jatuh Tempo">
											</div>
										</div>
									</div>
									<div class="col-md-9 col-sm-9">

										<!-- TABEL BARANG -->
										<div class="form-group has-feedback has-search">
											<span class="glyphicon glyphicon-search form-control-feedback"></span>
								            	<input name="search_data" class="form-control search-input" id="search_data" placeholder="Search / Scan Produk" type="text" onkeyup="scan_data();" disabled="">
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
															<th>Diskon (%)</th>
															<th>Qty</th>
															<th>Subtotal</th>
															<!-- <th>Aksi</th> -->
														</tr>
													</thead>
													<tbody id="row">
														<?php 
														$id = $this->uri->segment(3);
															$query = $this->db->query('SELECT * FROM tb_detail_pembelian WHERE id_pembelian='.$id)->result();
															foreach ($query as $data) {
																echo '<tr>
								                                <td><input type="hidden" class="id" name="id_barang[]" value="'.$data->id_barang.'">
								                                	<input type="text" name="sku_barang[]" value="'.$data->sku_barang.'" class="form-barang input-sm" disabled readonly></td>
								                                <td>
								                                	<input type="text" name="nama_barang[]" value="'.$data->nama_barang.'" class="form-barang input-sm" disabled readonly></td>
								                                <td>
								                                <input type="hidden" name="hargatmp[]" value="'.$data->harga.'" class="tmp_price">
								                                <input type="text" name="harga[]" value="'.$data->harga.'" class="form-barang price input-sm price" disabled>
								                                </td>
								                                <td><input type="number" name="diskon[]" value="'.$data->diskon.'" class="form-barang disc input-sm" disabled></td>
								                                <td><input type="number" name="quantity[]" value="'.$data->jumlah.'" class="form-barang qty input-sm" disabled></td>
								                                <td><input type="text" name="subtotal[]" value="'.$data->subtotal.'" class="form-barang subtotal input-sm"required readonly disabled></td>
								                            </tr>';
															}
														?>
													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="col-md-4 col-sm-4">
													<div class="form-group"  style="display:none;">
														<label>BAYAR</label>
														<input type="text" name="bayar" class="form-control input-sm" id="bayar" readonly="" value="<?php echo $detail->bayar;?>"> 
													</div>
												</div>
												<div class="col-md-4 col-sm-4">
													<div class="form-group"  style="display:none;">
														<label>KEMBALI</label>
														<input type="text" name="kembali"  class="form-control input-sm" id="kembali" readonly="" value="<?php echo $detail->sisa;?>"> 
													</div>
												</div>
												<div class="col-md-4 col-sm-4">
													<div class="form-group">
														<label>TOTAL</label>
														<input type="text" name="total" class="form-control input-sm" id="total" readonly="" value="<?php echo $detail->total;?>"> 
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
															<input type="text" name="nominal_giro" class="form-control input-sm" placeholder="Nominal Giro">
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
												<div class="form-group">
													<input type="submit" name="submit" class="btn btn-success btn-block btn-sm" value="Update Pembelian" id="btnUpdatePembelian">
												</div>
											</div>
										</div>
									</div>
		                        </div>
		                    </form>
				        </div>
				    </div>
				</div>
			<!-- 	</form> -->
<!-- END FORM PENJUALAN -->
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-ui.js"></script>
<!--<script src="<?php echo base_url();?>assets/spada/js/transaksi.js"></script>-->
<script src="<?php echo base_url().auto_version('assets/spada/js/transaksi.js'); ?>"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>