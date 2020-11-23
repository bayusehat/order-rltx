function auto_fill() {
		
    var el = $(this);
    
        $.ajax({
            url: base_url+"index.php/transaction/auto_fill",
            dataType: "json",
            type: "POST",
            data: {'sku_barang':el.val()},
               success: function (result) {
               if (el.val().length == result.sku_barang.length) {
                   $('.panel-heading').html('Update Barang '+result.nama_barang);
                   $("#form_barang").attr('action',base_url+'index.php/transaction/update_barang');
                   $("#id_barang").val(result.id_barang);
                   $("#nama").val(result.nama_barang);
                   $("#harga1").val(result.harga_modal);
                   $("#harga2").val(result.harga_jual);
                   $("#stok_barang").val(result.stok);
                   $("#satuan").val(result.satuan);
                   $("#id_kategori_barang").val(res.id_kategori_barang).trigger('change');
                   $(".stok_edit").show();
                   $(".btn-new").hide().attr('disabled',true);
                   $(".btn-update").show().attr('disabled',false);
               }else{
                   $('.panel-heading').html('Tambah Barang');
                   $("#form_barang").attr('action',base_url+'index.php/transaction/add_barang');
                   $("#id_barang").val("");
                   $("#nama").val("");
                   $("#harga1").val("");
                   $("#harga2").val("");
                   $("#stok_barang").val("");
                   $("#stok_barang").attr('readonly',false);
                   $(".stok_edit").hide();
                   $(".btn-new").show().attr('disabled',false);
                   $(".btn-update").hide().attr('disabled',true);
            }
        }
    });
    
}