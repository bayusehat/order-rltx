<?php $this->load->view('head');?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <a href="<?php echo base_url();?>index.php/admin/add_detail_stock_opname_baru/<?php echo $this->uri->segment(3);?>" style="margin-left: 10px" class="btn btn-success"><i class="fa fa-plus"></i> Add Detail Stock Opname</a>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <a href="<?php echo base_url();?>index.php/admin/stock_opname_baru" class="btn btn-danger right"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                        <div>
                            <?php echo $output; ?>
                        </div>
                    </div>
                </div>
<?php $this->load->view('foot');?>