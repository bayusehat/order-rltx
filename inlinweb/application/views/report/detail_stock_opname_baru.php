<?php
	$this->load->view('head_dash');
?>
        <div id="content-wrapper" class="group">
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <a href="<?php echo base_url();?>index.php/admin/stock_opname_baru" class="btn btn-danger right" style="margin-bottom: 20px"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <table width="50%">
                                            <tr>
                                                <td>Tanggal Stock Opname</td>
                                                <td>:</td>
                                                <td><?php echo date('d F Y',strtotime($stock->tanggal_stock_opname_baru));?></td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>:</td>
                                                <td><?php echo $stock->status;?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <br>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <th>Stok Database</th>
                                                    <th>Stok Gudang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                foreach ($detail as $data) {
                                                    echo
                                                    '<tr>
                                                        <td>'.$data->nama_barang.'</td>
                                                        <td>'.$data->stok_database.'</td>
                                                        <td>'.$data->stok_gudang.'</td>
                                                    </tr>';
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
	$this->load->view('foot_dash');
?>