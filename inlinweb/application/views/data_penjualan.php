<?php $this->load->view('head');?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-md-6 col-sm-6"> 
                                <a href="<?php echo base_url();?>index.php/admin/to_penjualan" style="margin-left: 10px" class="btn btn-success"><i class="fa fa-plus"></i> Add Penjualan</a>
                        </div>
                            <div class="col-md-6 col-sm-6">
                                <?php
                                    $url1 = base_url().'index.php/admin/penjualan_by_sales/'.$this->uri->segment(3);
                                    $url2 = base_url().'index.php/admin/penjualan_by_pelanggan/'.$this->uri->segment(3);
                                    $cur = current_url();

                                    if($cur == $url1){

                                        echo '<a href="'.base_url().'index.php/admin/data_sales_progress" class="btn btn-danger" style="float:right"><i class="fa fa-arrow-left"></i> Kembali</a>';

                                    }else if($cur == $url2){

                                        echo '<a href="'.base_url().'index.php/admin/data_pelanggan_progress" class="btn btn-danger" style="float:right"><i class="fa fa-arrow-left"></i> Kembali</a>';
                                    }else{
                                        echo '';
                                    }
                                ?>
                            </div>
                        </div>
                        <div>
                            <?php echo $output; ?>
                        </div>
                    </div>
                </div>
<?php $this->load->view('foot');?>