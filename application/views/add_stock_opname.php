<?php $this->load->view('head_dash');?>
		<div id="content-wrapper" class="group">
			<div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <a href="<?php echo base_url();?>index.php/admin/stock_opname" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                <br>
			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="panel panel-info">
			            <div class="panel-heading">Input Mutasi Barang</div>
				            <form method="post" action="<?php echo base_url();?>index.php/transaction/add_stock_opname_run" id="form_barang">
				            	<div class="panel-body">
                                    <div class="form-group">
                                        <p class="red">*Ketikan SKU / Nama barang pada input SKU</p>
                                    </div>
				            		<div class="form-group has-feedback">
				            			<label><i class="fa fa-search"></i> SKU </label>
				            			<input type="text" name="sku_barang" class="form-control" placeholder="SKU Barang" id="sku_barang" onkeyup="scan_data();">
                                            <input type="hidden" name="id_barang" id="id_barang">
                                                <!--<input type="hidden" name="stok" id="stok">-->
                                            <div id="suggestions">
                                                <div id="autoSuggestionsList">
                                                </div>
                                            </div>
				            		    </div>
                                    <div class="form-group">
                                        <label>Nama Barang</label>
                                        <input type="text" class="form-control" name="nama_barang" placeholder="Nama Barang" id="nama_barang" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Stok Saat Ini</label>
                                        <input type="text" class="form-control" name="stok" placeholder="Stok Saat Ini" id="stok" readonly>
                                    </div>
				            		<div class="form-group">
				            			<label>Stok Baru</label>
				            			<input type="text" name="stok_gudang" id="stok_gudang" class="form-control" placeholder="Stok Baru">
				            		</div>
				            		<div class="form-group">
				            			<label>Catatan</label>
				            			<input type="text" name="catatan" id="catatan" class="form-control" placeholder="Catatan">
				            			<input type="hidden" name="id_user" value="<?= $this->session->userdata('id_user');?>">
				            		</div>
						         </div>
							<div class="panel-footer">
								<input type="submit" name="submit" class="btn btn-success btn-block" value="Simpan Mutasi" onclick="return confirm('Simpan Mutasi?'); ">
								</div>
				            </form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/spada/js/stock_opname.js'?>"></script>
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