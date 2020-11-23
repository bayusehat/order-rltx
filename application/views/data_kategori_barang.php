<?php $this->load->view('head'); ?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12"> 
                            <?php if ($this->session->flashdata('berhasil')): ?>
                                <script>
                                    swal({
                                        title: "Success",
                                        text: "<?php echo $this->session->flashdata('berhasil'); ?>",
                                        timer: 2500,
                                        showConfirmButton: false,
                                        type: 'success'
                                        });
                                </script>
                            <?php endif; ?>

                            <?php
                                $url = base_url().'index.php/admin/barang/edit/'.$this->uri->segment(4);
                                $cur = current_url();

                                if($cur != $url){
                                    echo '<a href="'.base_url().'index.php/admin/to_barang" style="margin-left: 10px" class="btn btn-success"><i class="fa fa-plus"></i> Add / Edit Barang</a>';
                                }else{
                                    echo '';
                                }
                            ?>
                        </div>
                    </div>
                    <div id="out">
                            <?php echo $output; ?>
                        </div>
                    </div>
                </div> 
<?php $this->load->view('foot');?>