<?php $this->load->view('head_dash');?>
	<style type="text/css">
		.stok_edit,.btn-update{
			display: none;
		}
	</style>
		<div id="content-wrapper" class="group">
			<div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <a href="<?php echo base_url();?>index.php/admin/barang" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                <br>
			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="panel panel-info">
			            <div class="panel-heading">Tambah Barang</div>
				            <form method="post" action="<?php echo base_url();?>index.php/transaction/add_barang" id="form_barang">
				            	<div class="panel-body">
									<div class="form-group">
                                        <p class="red">*Jika SKU ada di database akan otomatis ke form Edit Barang</p>
                                    </div>
				            		<input type="hidden" name="id_barang" id="id_barang">
				            		<div class="form-group">
				            			<label>SKU</label>
				            			<input type="text" name="sku_barang" class="form-control" placeholder="SKU Barang" id="sku_barang" onkeyup="auto_fill.call(this);">
				            		</div>
									<div class="form-group">
										<label>Kategori Barang</label>
										<select name="id_kategori_barang" id="id_kategori_barang" class="form-control select2">
											<?php
												foreach ($kategori_barang as $i => $kb) {
													echo '<option value="'.$kb->id_kategori_barang.'">'.$kb->nama_kategori.'</option>';
												}
											?>
										</select>
									</div>
				            		<div class="form-group">
				            			<label>Nama Barang</label>
				            			<input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" id="nama">
				            		</div>
				            		<div class="form-group">
				            			<label>Harga Modal</label>
				            			<input type="text" name="harga_modal" class="form-control" placeholder="Harga Modal" id="harga1">
				            		</div>
				            		<div class="form-group">
				            			<label>Harga Jual</label>
				            			<input type="text" name="harga_jual" class="form-control" placeholder="Harga Jual" id="harga2">
				            		</div>
				            		<div class="form-group">
				            			<label>Stok</label>
				            			<input type="text" name="stok_barang" class="form-control" placeholder="Stok" id="stok_barang">
				            		</div>
				            		<div class="form-group stok_edit">
				            			<label>Perubahan Stok</label>
				            			<input type="text" name="stok_tambah" value="0" class="form-control" placeholder="Perubahan Stok">
				            		</div>
				            		<div class="form-group">
				            			<label>Satuan</label>
				            			<input type="text" name="satuan" class="form-control" placeholder="Satuan" id="satuan">
				            		</div>
						         </div>
							<div class="panel-footer">
								<input type="submit" name="submit" class="btn btn-success btn-block btn-new" value="Simpan Barang" onclick="return confirm('Simpan Barang?'); ">
								<input type="submit" name="submit" class="btn btn-primary btn-block btn-update" value="Update Barang" onclick="return confirm('Update Barang?');" disabled>
							</div>
				            </form>
			            	
						</div>
					</div>
				</div>
			</div>
<?php $this->load->view('foot_dash');?>

<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/spada/js/add_barang.js'?>"></script>
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