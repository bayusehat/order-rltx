<?php $this->load->view('head_dash'); ?>
        <!-- Page wrapper  -->
<div id="content-wrapper" class="group">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
            </div>
        </div>
            <div>
                <div class="row">
                    <div class="col-md-4 col-sm-12" style="margin-top:20px">
                        <div class="card">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-archive f-40"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 class="count"><?php echo $this->db->count_all_results('tb_barang'); ?></h2>
                                    <h4 class="m-b-0">Produk</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" style="margin-top:20px">
                        <div class="card">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-sign-out-alt f-40"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 class="count"><?php echo $this->db->count_all_results('tb_penjualan'); ?></h2>
                                    <h4 class="m-b-0">Penjualan</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" style="margin-top:20px">
                        <div class="card">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-sign-in-alt f-40"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 class="count"><?php echo $this->db->count_all_results('tb_pembelian'); ?></h2>
                                    <h4 class="m-b-0">Pembelian</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:20px">
                    <div class="col-md-12 col-sm-12">
                        <?php

                        if($jual != NULL){

                            foreach ($jual as $data) {
                                $jumlah[]= $data->m;
                                $tgl[] = date('F Y',strtotime($data->d));
                            }
                        }else{
                            return 0;
                        }

                        if($beli != NULL){
                            foreach ($beli as $data) {
                                $all[]= $data->m;
                                $nama[] = date('F Y',strtotime($data->d));
                            }
                        }else{
                            return 0;
                        }
                    ?>
                         <div class="card">
                            <div class="title">
                                <h3>Data Statistik Transaksi /bulan</h3>
                                <hr>
                            </div>
                            <canvas id="canvasbeli" width="1000" height="280"></canvas>
                            <script src="<?php echo base_url();?>assets/spada/js/jquery-2.1.3.js"></script>
                            <script src="<?php echo base_url();?>assets/spada/js/Chart.min.js"></script>
                            <script type="text/javascript">
                            $(document).ready(function () {
                                    var ctx = document.getElementById("canvasbeli").getContext("2d");

                                    var myChart = new Chart(ctx, {
                                            type: 'line',
                                            data: {
                                                  labels: <?php echo json_encode($nama);?>,
                                                  datasets: [{
                                                                label: 'Data Statistik Pembelian /bulan',
                                                                data: <?php echo json_encode($all);?>,
                                                                backgroundColor : "lightblue",
                                                                borderColor : "lightblue",
                                                                fill : false,
                                                                lineTension : 0,
                                                                pointRadius : 5
                                                             },
                                                            {
                                                                label: 'Data Statistik Penjualan /bulan',
                                                                data: <?php echo json_encode($jumlah);?>,
                                                                backgroundColor : "lightpink",
                                                                borderColor : "lightpink",
                                                                fill : false,
                                                                lineTension : 0,
                                                                pointRadius : 5
                                                            }]
                                                  },
                                            options: {
                                            scales: {
                                                  yAxes: [{
                                                        ticks: {
                                                             beginAtZero: true
                                                               }
                                                          }]
                                                    }
                                           }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
                <!-- <div class="row" style="margin-top:20px">
                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="title">
                                <h3>Penjualan Terbanyak (Sales)</h3>
                                <hr>
                            </div>
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-shopping-cart f-40"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <?php
                                        foreach ($sales as $data) {

                                            echo '<h2 class="count">'.$data->jumlah_penjualan.'</h2>
                                                <h4 class="m-b-0">'.$data->nama_sales.'</h4>';
                                     }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="title">
                                <h3>Pembelian Terbanyak (Supplier)</h3>
                                <hr>
                            </div>
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-user f-40"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <?php
                                        foreach ($supplier as $data) {

                                            echo '<h2 class="count">'.$data->jumlah_pembelian.'</h2>
                                                <h4 class="m-b-0">'.$data->nama_supplier.'</h4>';
                                     }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
        <!-- End Page wrapper  -->
<?php $this->load->view('foot_dash'); ?>