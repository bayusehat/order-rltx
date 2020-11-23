$(document).ready(function(){
	$("#search_data").focus();
	$(".tgl_lunas").datepicker({dateFormat: 'yy-mm-dd'});
	$('#tableCek').DataTable({
	  /*"lengthMenu": [[5, 10, 50, -1], [5, 10, 25, "All"]],*/
	  searching: false,
	  ordering: false,
	  paging: false,
	  info:false,
	});

});

function swal_success(msg) {
	swal({
        title: "Berhasil",
        text: msg,
        timer: 2500,
        showConfirmButton: false,
        type: 'success'
    });
}

function swal_failed(msg) {
	swal({
        title: "Gagal",
        text: msg,
        timer: 2500,
        showConfirmButton: false,
        type: 'error'
    });
}

var i = 0;
var no = 1;
var qty = {};

		function add_barang(e){
			var produk_id    = $(e).data("produk-id");
			var produk_kode  = $(e).data("produkkode");
			var produk_nama  = $(e).data("produknama");
			var produk_harga = $(e).data("produkharga");
			
			var qtyItem 	 = $("#qty"+produk_id);
			var jml 		 = 1;
			var url      	 = window.location.href;
			var a = '';

			var produk_harga_modal = 0;
			var display_harga_modal = '';

			if(url == base_url+'index.php/admin/to_penjualan'){
				 a += '<a href="#" data-toggle="modal" data-target="#modalCek" onclick="detail_harga_penjualan('+produk_id+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
				 produk_harga_modal = $(e).data("produkhargamodal");
				 display_harga_modal = '<em style="font-size:0.8em;">hm: '+produk_harga_modal+'</em>';
			}else if(url == base_url+'index.php/admin/to_pembelian'){
				 a += '<a href="#" data-toggle="modal" data-target="#modalCekBeli" onclick="detail_harga_pembelian('+produk_id+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
			}else{
				 a += '-';
			}

			if ($("#row tr td input[value='"+produk_id+"']").length == 0 && qtyItem.length == 0){
				var subtotal = jml * produk_harga;
					$("#row").append('<tr>'+
	                                '<td style="width:10%"><input type="hidden" class="id" name="id_barang['+i+']" value="'+produk_id+'"><input type="text" name="sku_barang['+i+']" value="'+produk_kode+'" class="form-barang input-sm" required readonly></td>'+
	                                '<td style="width:30%"><input type="hidden" name="nama_barang['+i+']" value="'+produk_nama+'" class="form-barang input-sm" required readonly><span style="font-size:0.8em">'+produk_nama+'</span></td>'+
	                                '<td style="width:15%">'+
	                                '<input type="hidden" name="hargatmp['+i+']" value="'+produk_harga+'" class="tmp_price'+produk_id+'">'+
	                                '<input type="text" name="harga['+i+']" value="'+produk_harga+'" class="form-control price input-sm price'+produk_id+'" onkeyup="update_price();" required>'+display_harga_modal+
	                                '</td>'+
	                                '<td>'+a+'</td>'+
	                                '<td style="width:15%"><input type="number" name="diskon['+i+']" value="0" class="form-control input-sm disc'+produk_id+'" onkeyup="update_diskon('+produk_id+');" required></td>'+
	                                '<td style="width:10%"><input type="number" name="quantity['+i+']" value="'+jml+'" class="form-control qty input-sm" id="qty'+produk_id+'" onkeyup="update_qty();" required></td>'+
	                                '<td style="width:15%"><input type="text" name="subtotal['+i+']" value="'+subtotal+'" class="form-barang subtotal input-sm" id="sub'+produk_id+'" required readonly></td>'+
	                                '<td><button type="button" class="btn btn-danger btn-sm del" onclick="hapus_row(this);"><i class="fa fa-trash"></i></button> </td>'+
	                            '</tr>');
	            					i++;
	            					qtyItem.val(1); 
	            				update_qty();     
	                            total();
	                            ppn();
	                            $('tbody#row tr:last td:first input').focus();
	                            $("#suggestions").hide();
	                            $("#search_data").val("").focus();
				}else{
     				var currentVal = parseFloat(qtyItem.val());
				    if(!isNaN(currentVal) && qtyItem.length == 1){
				     	qtyItem.attr('value',currentVal+1) ;
				    }
				    $("#sub"+produk_id).val( qtyItem.val()*produk_harga);
				    update_qty();
            		total();
            		ppn();
	                $("#suggestions").hide();
	                $("#search_data").val("");
				}
            }

		function scan_data(){
                var input_data = $('#search_data').val();

                if (input_data.length === 0)
                {
                    $('#suggestions').hide();
                }
                else
                {
                    var post_data = {
                        'search_data': input_data,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                    };

                    $.ajax({
                        type: "POST",
                        url: base_url+"index.php/admin/get_scan_barang",
                        data: post_data,
                        success: function (data) {
                            if (data.length > 0) {
                                $('#suggestions').show();
                                $('#autoSuggestionsList').addClass('auto_list');
                                $('#autoSuggestionsList').html(data);
                            }
                        }
                    });

                }
            }

    	$("#search_data").keyup(function(){
			   var el = $(this);
			   
			        $.ajax({
			            url: base_url+"index.php/admin/scan_barang",
			            dataType: "json",
			            type: "POST",
			            data: {'search_data':el.val()},
			            success: function (result) {
			            	var qtyItem = $("#qty"+result.id_barang);
			            	var jml 	= 1;
			            	var url     = window.location.href;
							var a 		= '';

							if(url == base_url+'index.php/admin/to_penjualan'){
								 a += '<a href="#" data-toggle="modal" data-target="#modalCek" onclick="detail_harga_penjualan('+result.id_barang+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
							}else if(url == base_url+'index.php/admin/to_pembelian'){
								 a += '<a href="#" data-toggle="modal" data-target="#modalCekBeli" onclick="detail_harga_pembelian('+result.id_barang+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
							}else{
								 a += '-';
							}

			            	if (el.val().length == result.sku_barang.length) {
							    if ($("#row tr td input[value='"+result.id_barang+"']").length == 0 && qtyItem.length == 0){
									$("#row").append('<tr>'+
					                                '<td style="width:10%"><input type="hidden" class="id" name="id_barang['+i+']" value="'+result.id_barang+'"><input type="text" name="sku_barang['+i+']" value="'+result.sku_barang+'" class="form-barang input-sm" required readonly></td>'+
					                                '<td style="width:30%"><input type="hidden" name="nama_barang['+i+']" value="'+result.nama_barang+'" class="form-barang input-sm" required readonly><span style="font-size:0.8em">'+result.nama_barang+'</span></td>'+
					                                '<td style="width:15%">'+
					                                '<input type="hidden" name="hargatmp['+i+']" value="'+result.harga_jual+'" class="tmp_price'+result.id_barang+'">'+
					                                '<input type="text" name="harga['+i+']" value="'+result.harga_jual+'" class="form-control price input-sm price'+result.id_barang+'" onkeyup="update_price();" required><em style="font-size:0.8em;">hm: '+result.harga_modal+'</em></td>'+
					                                '<td>'+a+'</td>'+
					                                '<td style="width:15%"><input type="number" name="diskon['+i+']" value="0" class="form-control input-sm disc'+result.id_barang+'" onkeyup="update_diskon('+result.id_barang+');" required></td>'+
					                                '<td style="width:10%"><input type="number" name="quantity['+i+']" value="'+jml+'" class="form-control qty input-sm" id="qty'+result.id_barang+'" onkeyup="update_qty();" required></td>'+
					                                '<td style="width:15%"><input type="text" name="subtotal['+i+']" value="'+jml * result.harga_jual+'" class="form-barang subtotal input-sm" id="sub'+result.id_barang+'" required readonly></td>'+
					                                '<td><button type="button" class="btn btn-danger btn-sm del" onclick="hapus_row(this);"><i class="fa fa-trash"></i></button> </td>'+
					                            '</tr>');
					            					i++;
					                            total();
					                            ppn();
					                            $('tbody#row tr:last td:first input').focus();
					                            $("#suggestions").hide();
						                        $("#search_data").val("").focus();
								}else{
							
				     				var currentVal = parseFloat(qtyItem.val());
								    console.log(currentVal);
								    if(!isNaN(currentVal) && qtyItem.length == 1){
								     	qtyItem.attr('value',currentVal+1) ;
								     }
				     				$("#sub"+result.id_barang).val(qtyItem.val()*result.harga_jual);
									total();
									ppn();
									$("#suggestions").hide();
					                $("#search_data").val("");
								}
						}else{
			        }
			    }
			});
		});
		
		function scan_data_beli(){
                var input_data = $('#search_data_beli').val();

                if (input_data.length === 0)
                {
                    $('#suggestions').hide();
                }
                else
                {
                    var post_data = {
                        'search_data': input_data,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                    };

                    $.ajax({
                        type: "POST",
                        url: base_url+"index.php/admin/get_scan_barang_beli",
                        data: post_data,
                        success: function (data) {
                            if (data.length > 0) {
                                $('#suggestions').show();
                                $('#autoSuggestionsList').addClass('auto_list');
                                $('#autoSuggestionsList').html(data);
                            }
                        }
                    });

                }
            }

    	$("#search_data_beli").keyup(function () {
			   var el = $(this);
			   
			        $.ajax({
			            url: base_url+"index.php/admin/scan_barang_beli",
			            dataType: "json",
			            type: "POST",
			            data: {'search_data':el.val()},
			            success: function (result) {
			            	var qtyItem = $("#qty"+result.id_barang);
			            	var jml 	= 1;
			            	var url     = window.location.href;
							var a 		= '';

							if(url == base_url+'index.php/admin/to_penjualan'){
								 a += '<a href="#" data-toggle="modal" data-target="#modalCek" onclick="detail_harga_penjualan('+result.id_barang+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
							}else if(url == base_url+'index.php/admin/to_pembelian'){
								 a += '<a href="#" data-toggle="modal" data-target="#modalCekBeli" onclick="detail_harga_pembelian('+result.id_barang+');" class="btn btn-info btn-sm"><i class="fa fa-list"></i></a>';
							}else{
								 a += '-';
							}

			            	if (el.val().length == result.sku_barang.length) {
							    if ($("#row tr td input[value='"+result.id_barang+"']").length == 0 && qtyItem.length == 0){
									$("#row").append('<tr>'+
					                                '<td style="width:10%"><input type="hidden" class="id" name="id_barang['+i+']" value="'+result.id_barang+'"><input type="text" name="sku_barang['+i+']" value="'+result.sku_barang+'" class="form-barang input-sm" required readonly></td>'+
					                                '<td style="width:30%"><input type="hidden" name="nama_barang['+i+']" value="'+result.nama_barang+'" class="form-barang input-sm" required readonly><span style="font-size:0.8em;">'+result.nama_barang+'</span></td>'+
					                                '<td style="width:15%">'+
					                                '<input type="hidden" name="hargatmp['+i+']" value="'+result.harga_modal+'" class="tmp_price'+result.id_barang+'">'+
					                                '<input type="text" name="harga['+i+']" value="'+result.harga_modal+'" class="form-control price input-sm price'+result.id_barang+'" onkeyup="update_price();" required></td>'+
					                                '<td>'+a+'</td>'+
					                                '<td style="width:15%"><input type="number" name="diskon['+i+']" value="0" class="form-control input-sm disc'+result.id_barang+'" onkeyup="update_diskon('+result.id_barang+');" required></td>'+
					                                '<td style="width:10%"><input type="number" name="quantity['+i+']" value="'+jml+'" class="form-control qty input-sm" id="qty'+result.id_barang+'" onkeyup="update_qty();" required></td>'+
					                                '<td style="width:15%"><input type="text" name="subtotal['+i+']" value="'+jml * result.harga_modal+'" class="form-barang subtotal input-sm" id="sub'+result.id_barang+'" required readonly></td>'+
					                                '<td><button type="button" class="btn btn-danger btn-sm del" onclick="hapus_row(this);"><i class="fa fa-trash"></i></button> </td>'+
					                            '</tr>');
					            					i++;
					                            total();
					                            ppn();
					                            $('tbody#row tr:last td:first input').focus();
					                            $("#autoSuggestionsList").hide();
						                        $("#search_data").val("").focus();
								}else{
							
				     				var currentVal = parseFloat(qtyItem.val());
								    console.log(currentVal);
								    if(!isNaN(currentVal) && qtyItem.length == 1){
								     	qtyItem.attr('value',currentVal+1) ;
								     }
				     				$("#sub"+result.id_barang).val(qtyItem.val()*result.harga_modal);
									total();
									ppn();
									$("#suggestions").hide();
					                $("#search_data").val("");
								}
						}else{

			        	/*if(el.val().length > result.sku_barang.length){
			        		alert('Barang tidak tersedia');
			        	}*/
			        }
			    }
			});
		});

		function hapus_row(e) {
            	$(e).parent().parent().remove();
            	total();
            	ppn();
            }

		function update_qty() {
            	total();
            	update_amounts();
			    $('.qty').keyup(function() {
			        update_amounts();
			        total();
			    });
            }

        function update_price() {
        		total();
        		discount();
            	update_amounts();
			    $('.price').keyup(function() {
			        update_amounts();
			        discount();
			        total();
			    });
        }

        function discount() {
			var sum = 0;
			    $('#myTable > tbody  > tr').each(function() {
			    	var id = $(this).find('.id').val();
			    	var disc = $(this).find('.disc'+id).val();
			        var price = $(this).find('.price'+id).val();
			        var amount_disc = (disc/100) * price;
			        var aft_disc = (price-amount_disc);
			        sum+=aft_disc; 
			        $(this).find('.tmp_price'+id).attr('value',aft_disc);
			 });
		}

        function update_diskon(id) {
        		total();
            	discount();
            	update_amounts();
			    $('.disc'+id).keyup(function() {
			    	discount();
			    	update_amounts();
			        total();
			    });
        }

		function total() {
            var sum = 0;

            $(".subtotal").each(function() {
                var value = $(this).val();
                
                if(!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });

            $("#total").val(sum);
        	}

		function update_amounts(){
			    var sum = 0;
			    $('#myTable > tbody  > tr').each(function() {
					var id = $(this).find('.id').val();
			        var qty = $(this).find('.qty').val();
			        var price = $(this).find('.tmp_price'+id).val();
			        var amount = (qty*price)
			        sum+=amount;
			        $(this).find('.subtotal').val(amount);
			    });
			  
			}

		function kembali() {
				var bayar = $("#bayar").val();
				var total = $("#total").val();
				var kembali = bayar-total;

				$("#kembali").val(kembali);
			}

		$('#bayar').keyup(function() {
			        update_amounts();
			        // total();
			        kembali();
			});
		$('form select[name=ppn]').change(function(){
                if ($(this).find(':selected').data('ppn') == 'Include'){

                	var b_total = parseInt($('#total').val());
                	var ppn = 0.1;
                	var jumlah_ppn = ppn*b_total;
                	var new_total = b_total+jumlah_ppn;

                	$("#total").val(new_total);
                	$("#show_nominal").removeClass('hide');
                	$("#nominal_ppn").val(jumlah_ppn);

                }else{
                	$("#show_nominal").addClass('hide');
                   	total();
                }
            });

		function ppn() {

			var tbody = $("#myTable tbody");

			if(tbody.children().length == 0){
				$("#ppn").attr('readonly',true);
				$("#bayar").attr('readonly',true);
			}else{
				$("#ppn").attr('readonly',false);
				$("#bayar").attr('readonly',false);
			}
		}

		$("#sama").click(function(){
			var total = $("#total").val();
			if($(this).is(':checked')){
				$("#sama").attr('checked',true);
				$("#nominal_giro").val(total);
			}else{
				$("#sama").attr('checked',false);
				$("#nominal_giro").val("0");
			}
		});

		$('form select[name=id_payment]').change(function(){
                if ($(this).find(':selected').data('cash') == 'Yes'){
	                $('#back').hide();
                    $('#pay').hide();
                    
                }else{
                    //$('#back').show();
                    //$('#pay').show();
                }
            });

        $('form select[name=id_payment]').change(function(){
                if ($(this).find(':selected').data('cash') == 'No'){
	                $('#back').hide();
                    $('#pay').hide();
                    
                }else{
                    //$('#back').show();
                    //$('#pay').show();
                }
            }); 

		$('form select[name=id_payment]').change(function(){
                if ($(this).find(':selected').data('type') == 'Giro'){
	                $('.giro').show();
	                $("#metode").attr('value','Giro');
	                $('#back').hide();
                    $('#pay').hide();                   
                }else{
                     $('.giro').hide();
                     $("#metode").attr('value','');
                }
            });

		$('form select[name=status]').change(function(){
			//alert($(this).find(':selected').data('value'));
			if ($('form select[name=status]').val() == 'Lunas'){
	                $('#tanggal_lunas').show();
	                $('#tanggal_jatuh_tempo').hide();                  
                }else if($('form select[name=status]').val() == 'Belum Terbayar'){
                	$('#tanggal_jatuh_tempo').show();
                	$('#tanggal_lunas').hide()
                }else{
                	$('#tanggal_jatuh_tempo').hide();
                	$('#tanggal_lunas').hide()
                }
		});
                
		$("#save_jual").click(function(event){
			event.preventDefault();
			var form_data = $('form#formPenjualan').serialize();
			var conf = confirm('Apakah anda yakin menyimpan?');
			if(conf){
				if($("#id_pelanggan").val()==''){
					$("#id_pelanggan").focus();
					swal_failed('Pelanggan harus dipilih');
				}else if($("#id_sales").val()==''){
					$("#id_sales").focus();
					swal_failed('Sales harus dipilih');
				}else if($("#id_payment").val()==''){
					$("#id_payment").focus();
					swal_failed('Payment harus dipilih');
				}else if($("#ppn").val()==''){
					$("#ppn").focus();
					swal_failed('PPN harus dipilih');
				}else if($("#status").val() == ''){
					$("#status").focus();
					swal_failed('Status harus dipilih');
				}else{
						$.ajax({
						type : "POST",
						url : base_url+"index.php/admin/simpan_penjualan",
						dataType : "JSON",
						data : form_data,
						success:function(message){
							// alert('Sukses');
							$("form").trigger('reset');
							$("#row").empty();
							swal({
				                title: "Berhasil",
				                text: "Transaksi berhasil diinputkan!",
				               	type: 'success',
				                showConfirmButton: false,
				                timer: 2000
				            });
				            window.setTimeout(function(){ 
							    location.reload();
							} ,2000);  
						},
						error:function(message){
							swal({
				                title: "Gagal",
				                text: "Transaksi gagal diinputkan!" + data,
				                timer: 2000,
				                showConfirmButton: false,
				                type: 'error'
				            });
				            /*window.setTimeout(function(){ 
							    location.reload();
							} ,2000);*/
						},
					});
				}
			}else{

			}
		});

		$("#save_beli").click(function(event){
			event.preventDefault();
			var form_data = $('form#formPembelian').serialize();
			var conf = confirm('Apakah anda yakin menyimpan?');

			if(conf){
				if($("#id_supplier").val()==''){
					$("#id_supplier").focus();
					swal_failed('Supplier harus dipilih');
				}else if($("#id_payment").val()==''){
					$("#id_payment").focus();
					swal_failed('Payment harus dipilih');
				}else if($("#ppn").val()==''){
					$("#ppn").focus();
					swal_failed('PPN harus diplih');
				}else if($("#status").val() == ''){
					$("#status").focus();
					swal_failed('Status harus dipilih');
				}else{
						$.ajax({
						type : "POST",
						url : base_url+"index.php/admin/simpan_pembelian",
						dataType : "JSON",
						data : form_data,
						success:function(data){
							
							$("form").trigger('reset');
							$("#row").empty();
							swal({
				                title: "Berhasil",
				                text: "Pembelian berhasil diinputkan!",
				                timer: 2000,
				                showConfirmButton: false,
				                type: 'success'
				            });
				            window.setTimeout(function(){ 
							    location.reload();
							} ,2000);
						},
						error:function(data){
							swal({
				                title: "Gagal",
				                text: "Pembelian gagal diinputkan, Cek form anda!",
				                timer: 2000,
				                showConfirmButton: false,
				                type: 'error'
				            });
				            /*window.setTimeout(function(){ 
							    location.reload();
							} ,2000);*/
						},
					})
				}
			}else{

			}
		});

		function detail_harga_penjualan(id_barang) {
			var url_pelanggan = '';
			if($("#id_pelanggan").val() > 0){
				var id_pelanggan = $("#id_pelanggan").val();
				url_pelanggan = '/' + id_pelanggan;
			}
			$.ajax({
				type : 'POST',
				url : base_url+'index.php/admin/detail_harga_penjualan/'+id_barang+url_pelanggan,
				dataType : "JSON",
				success:function(data){
					html = '';
					var i = 0;
					for(i=0; i<data.length; i++){
                        html += '<tr>'+
                                    '<td>'+data[i].tanggal_penjualan+'</td>'+
                                    '<td>'+data[i].nama_pelanggan+'</td>'+
                                    '<td>Rp '+data[i].harga.replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td>'+
                                    '<td>'+data[i].jumlah+'</td>'+
                                '</tr>';
                    }
                    $("#detail_harga").html(html);
				}
			})
		}

		function detail_harga_pembelian(id_barang) {
			var url_supplier = '';
			if($("#id_supplier").val() > 0){
				var id_supplier = $("#id_supplier").val();
				url_supplier = '/' + id_supplier;
			}
			$.ajax({
				type : 'POST',
				url : base_url+'index.php/admin/detail_harga_pembelian/'+id_barang+url_supplier,
				dataType : "JSON",
				success:function(data){
					html = '';
					var i = 0;
					for(i=0; i<data.length; i++){
                        html += '<tr>'+
                                    '<td>'+data[i].tanggal_pembelian+'</td>'+
                                    '<td>'+data[i].nama_supplier+'</td>'+
                                    '<td>Rp '+data[i].harga.replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td>'+
                                    '<td>'+data[i].jumlah+'</td>'+
                                '</tr>';
                    }
                    $("#detail_harga").html(html);
				}
			})
		}


