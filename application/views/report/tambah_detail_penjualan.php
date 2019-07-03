<form action="post" id="formTambahDetail">
    <div class="modal fade" id="modalTambahDetailPenjualan" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Detail Penjualan<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button></h4>
          </div>
          <div class="modal-body">
            <div class="form-group has-feedback has-search">
                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                <input name="search_data_tambah" class="form-control search-input" id="search_data_tambah" placeholder="Search / Scan Produk" type="text" onkeyup="scan_data_tambah();">
                      <div id="suggestions">
                          <div id="autoSuggestionsList">
                        </div>
                  </div>
                </div>
                    <div class="scroll">
                        <table class="table table-striped table-bordered" id="myTableTambah">
                          <thead>
                            <tr>
                              <th>SKU</th>
                              <th>Produk</th>
                              <th>Harga</th>
                              <th>Diskon (%)</th>
                              <th>Qty</th>
                              <th>Subtotal</th>
                              <!-- <th>Aksi</th> -->
                            </tr>
                          </thead>
                          <tbody id="rowTambah">
                            
                        </tbody>
                    </table>
                </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block" id="btn-save" onclick="return save_pelabuhan();">Simpan</button>
          </div>
        </div>
      </div>
    </div>
</form>