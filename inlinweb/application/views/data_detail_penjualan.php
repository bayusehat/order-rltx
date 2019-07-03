<?php $this->load->view('head');?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                              <a href="<?php echo base_url();?>index.php/admin/penjualan" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                        <div>
                            <?php echo $output; ?>
                        </div>
                    </div>
                </div>
<?php $this->load->view('foot');?>