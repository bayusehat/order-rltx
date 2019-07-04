<?php $this->load->view('head_dash');?>
    <div id="content-wrapper" class="group">
            <div id="page-wrapper">
              <!-- <div class="card"> -->
                <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <a href="<?php echo base_url();?>index.php/admin/detail_penjualan/<?php echo $detail->id_pembelian;?>" class="btn btn-danger" style="float: right;"><i class="fa fa-arrow-left"></i> Kembali ke Detail Penjualan <?php echo $detail->nomor_pembelian;?></a>
                        </div>
                    </div>
              <!-- </div> -->
                <br>
          <div>
      <!-- ROW 1 -->
        <form method="post" action="<?php echo base_url();?>index.php/transaction/update_detail_pembelian/<?php echo $this->uri->segment(3);?>">
            <div class="card">
                <div class="row">
                  <div class="col-md-12 col-sm-12">
                    <!-- TABEL BARANG -->
                    <div class="form-group has-feedback has-search">
                      <span class="glyphicon glyphicon-search form-control-feedback"></span>
                              <input name="search_data_beli" class="form-control search-input" id="search_data_beli" placeholder="Search / Scan Produk" type="text" onkeyup="scan_data_beli();">
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
                              <th>Cek</th>
                              <th>Diskon (%)</th>
                              <th>Qty</th>
                              <th>Subtotal</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody id="row">
                            <?php 
                            $id = $this->uri->segment(3);
                              $query = $this->db->query('SELECT * FROM tb_detail_pembelian WHERE id_pembelian='.$id)->result();
                              foreach ($query as $data) {
                                echo '<tr>
                                        <td style="width:10%">
                                            <input type="hidden" name="id_detail_pembelian[]" value="'.$data->id_detail_pembelian.'">
                                            <input type="hidden" class="id" name="id_barang[]" value="'.$data->id_barang.'">
                                            <input type="text" name="sku_barang[]" value="'.$data->sku_barang.'" class="form-barang input-sm" required readonly>
                                        </td>
                                        <td style="width:30%">
                                            <input type="text" name="nama_barang[]" value="'.$data->nama_barang.'" class="form-barang input-sm" required readonly>
                                            <span style="font-size:0.8em"></span>
                                        </td>
                                        <td style="width:15%">
                                            <input type="hidden" name="hargatmp[]" value="'.$data->harga.'" class="tmp_price'.$data->id_barang.'">
                                            <input type="text" name="harga[]" value="'.$data->harga.'" class="form-control price input-sm price'.$data->id_barang.'" onkeyup="update_price();" required>
                                            <em style="font-size:0.8em;">hm:</em>
                                        </td>
                                        <td></td>
                                        <td style="width:15%">
                                            <input type="number" name="diskon[]" value="0" class="form-control input-sm disc'.$data->id_barang.'" onkeyup="update_diskon('.$data->id_barang.');" required>
                                        </td>
                                        <td style="width:10%">
                                            <input type="number" name="quantity[]" value="'.$data->jumlah.'" class="form-control qty input-sm" id="qty'.$data->id_barang.'" onkeyup="update_qty();" required>
                                        </td>
                                        <td style="width:15%">
                                            <input type="text" name="subtotal[]" value="'.$data->subtotal.'" class="form-barang subtotal input-sm" id="sub'.$data->id_barang.'" required readonly>
                                        </td>
                                        <td>
                                            -
                                        </td>
                                    </tr>';
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                      <div>
                        <div class="form-group">
                          <input type="submit" name="submit" id="btnUpdateDetailPenjualan" class="btn btn-info btn-block" value="Update Detail Penjualan">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url();?>assets/spada/js/jquery-ui.js"></script>
<!--<script src="<?php echo base_url();?>assets/spada/js/transaksi.js"></script>-->
<script src="<?php echo base_url().auto_version('assets/spada/js/transaksi.js'); ?>"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>