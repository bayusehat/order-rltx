<?php $this->load->view('head');?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                            <a href="<?php echo base_url();?>index.php/admin/to_pembelian" style="margin-left: 10px" class="btn btn-success"><i class="fa fa-plus"></i> Add Pembelian</a>
                        </div>
                    </div>
                    <div>
                        <?php echo $output; ?>
                        </div>
                    </div>
                </div>
<?php $this->load->view('foot');?>