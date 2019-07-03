$("#btnQuickAddSupplier").click(function(event){
			event.preventDefault();
			var conf = confirm('Apakah anda yakin menyimpan?');

			var nama_supplier = $("#quick_nama_supplier").val();
			var nama_perusahaan_supplier = $("#quick_nama_perusahaan_supplier").val();
			var alamat_supplier = $("#quick_alamat_supplier").val();
			var kota_supplier = $("#quick_kota_supplier").val();
			var nomor_telepon_supplier = $("#quick_nomor_telepon_supplier").val();
			var email_supplier = $("#quick_email_supplier").val();

			if(conf){
				if($("#quick_nama_supplier").val() == ''){
					$("#quick_nama_supplier").focus();
					swal_failed('Nama Supplier tidak boleh kosong');
				}else if($("#quick_nama_perusahaan_supplier").val() == ''){
					$("#quick_nama_perusahaan_supplier").focus();
					swal_failed('Nama perusahaan supplier tidak boleh kosong');
				}else if($("#quick_alamat_supplier").val() == ''){
					$("#quick_alamat_supplier").focus();
					swal_failed('Alamat supplier tidak boleh kosong');
			    }else if($("#quick_kota_supplier").val() == ''){
					$("#quick_kota_supplier").focus();
					swal_failed('Kota supplier tidak boleh kosong');
				}else if($("#quick_nomor_telepon_supplier").val() == ''){
					$("#quick_nomor_telepon_supplier").focus();
					swal_failed('Nomor telepon supplier tidak boleh kosong');
				}else if($("#quick_email_supplier").val() == ''){
					$("#quick_email_supplier").focus();
					swal_failed('E-mail supplier tidak boleh kosong');
				}else{
					$.ajax({
						type : "POST",
						url  : base_url + "index.php/transaction/add_supplier",
						dataType : "json",
						data : {
							nama_supplier : nama_supplier,
							nama_perusahaan_supplier : nama_perusahaan_supplier,
							alamat_supplier : alamat_supplier,
							kota_supplier : kota_supplier,
							nomor_telepon_supplier : nomor_telepon_supplier,
							email_supplier : email_supplier
						},
						success:function(data){
							swal_success(data.msg);
							$("#formQuickAddSupplier").trigger('reset');
							setTimeout(function(){window.location.reload();},1000);
							$("#modalAddSupplier").fadeOut('slow');
						},
						error:function(data){
							swal_failed(data.msg);
							$("#formQuickAddSupplier").trigger('reset');
						}
					})
				}
			}else{

			}
		});

$("#btnQuickAddPelanggan").click(function(event){
		event.preventDefault();
			var conf = confirm('Apakah anda yakin menyimpan?');

			var nama_pelanggan = $("#quick_nama_pelanggan").val();
			var nama_perusahaan_pelanggan = $("#quick_nama_perusahaan_pelanggan").val();
			var alamat_pelanggan = $("#quick_alamat_pelanggan").val();
			var kota_pelanggan = $("#quick_kota_pelanggan").val();
			var nomor_telepon_pelanggan = $("#quick_nomor_telepon_pelanggan").val();
			var email_pelanggan = $("#quick_email_pelanggan").val();

			if(conf){
				if($("#quick_nama_pelanggan").val() == ''){
					$("#quick_nama_pelanggan").focus();
					swal_failed('Nama pelanggan tidak boleh kosong');
				}else if($("#quick_nama_perusahaan_pelanggan").val() == ''){
					$("#quick_nama_perusahaan_pelanggan").focus();
					swal_failed('Nama perusahaan pelanggan tidak boleh kosong');
				}else if($("#quick_alamat_pelanggan").val() == ''){
					$("#quick_alamat_pelanggan").focus();
					swal_failed('Alamat pelanggan tidak boleh kosong');
			    }else if($("#quick_kota_pelanggan").val() == ''){
					$("#quick_kota_pelanggan").focus();
					swal_failed('Kota_pelanggan tidak boleh kosong');
				}else if($("#quick_nomor_telepon_pelanggan").val() == ''){
					$("#quick_nomor_telepon_pelanggan").focus();
					swal_failed('Nomor telepon pelanggan tidak boleh kosong');
				}else if($("#quick_email_pelanggan").val() == ''){
					$("#quick_email_pelanggan").focus();
					swal_failed('E-mail pelanggan tidak boleh kosong');
				}else{
					$.ajax({
						type : "POST",
						url  : base_url + "index.php/transaction/add_pelanggan",
						dataType : "json",
						data : {
							nama_pelanggan : nama_pelanggan,
							nama_perusahaan_pelanggan : nama_perusahaan_pelanggan,
							alamat_pelanggan : alamat_pelanggan,
							kota_pelanggan : kota_pelanggan,
							nomor_telepon_pelanggan : nomor_telepon_pelanggan,
							email_pelanggan : email_pelanggan
						},
						success:function(data){
							swal_success(data.msg);
							$("#formQuickAddPelanggan").trigger('reset');
							$("#modalAddSupplier").fadeOut('slow');
							setTimeout(function(){window.location.reload();},1000);
						},
						error:function(data){
							swal_failed(data.msg);
							$("#formQuickAddPelanggan").trigger('reset');
						}
					})
				}
			}else{

			}
	});

	$("#btnQuickAddPayment").click(function(event){
		event.preventDefault();
		var conf = confirm('Apakah anda yakin menyimpan?');
		var nama_payment = $("#quick_nama_payment").val();
		var cash_payment = $("#quick_cash_payment").val();
		var status = $("#quick_status").val();

		if(conf){
			if($("#quick_nama_payment").val() == ''){
				$("#quick_nama_payment").focus();
				swal_failed('Nama payment tidak boleh kosong');
			}else if($("#quick_cash_payment").val() == ''){
				$("#quick_cash_payment").focus();
				swal_failed('Cash payment harus dipilih');
			}else if($("#quick_status").val() == ''){
				$("#quick_status").focus();
				swal_failed('Status harus dipilih');
			}else{
				$.ajax({
					type : "POST",
					url : base_url + "index.php/transaction/add_payment",
					dataType : "json",
					data : {
						nama_payment : nama_payment,
						cash_payment : cash_payment,
						status : status
					},
					success:function(data){
						swal_success(data.msg);
						$("#formQuickAddPayment").trigger('reset');
						$("#modalAddPayment").fadeOut('slow');
						setTimeout(function(){window.location.reload();},1000);
					},
					error:function(data){
						swal_failed(data.msg);
						$("#formQuickAddPayment").trigger('reset');
					}
				});
			}
		}else{

		}
	});

	$("#btnQuickAddBarang").click(function(event){
		event.preventDefault();
		var conf = confirm('Apakah anda yakin menyimpan?');
		var sku_barang = $("#quick_sku_barang").val();
		var nama_barang= $("#quick_nama_barang").val();
		var harga_modal= $("#quick_harga_modal").val();
		var harga_jual = $("#quick_harga_jual").val();
		var stok = $("#quick_stok").val();
		var satuan = $("#quick_satuan").val();

		if(conf){
			if($("#quick_sku_barang").val() == ''){
				$("#quick_sku_barang").focus();
				swal_failed('SKU barang tidak boleh kosong');
			}else if($("#nama_barang").val() == ''){
				$("#quick_nama_barang").focus();
				swal_failed('Nama barang tidak boleh kosong')
			}else if($("#quick_harga_modal").val() == ''){
				$("#quick_harga_modal").focus();
				swal_failed('Harga modal tidak boleh kosong');
			}else if($("#quick_harga_jual").val() == ''){
				$("#quick_harga_jual").focus();
				swal_failed('Harga jual tidak boleh kosong');
			}else if($("#quick_stok").val() == ''){
				$("#quick_stok").focus();
				swal_failed('Stok tidak boleh kosong');
			}else if($("#quick_satuan").val() == ''){
				$("#quick_satuan").focus();
				swal_failed('Satuan tidak boleh kosong');
			}else{
				$.ajax({
					type : "POST",
					url : base_url+"index.php/transaction/quick_add_barang",
					dataType : "json",
					data : {
						sku_barang : sku_barang,
						nama_barang : nama_barang,
						harga_modal : harga_modal,
						harga_jual : harga_jual,
						stok_barang : stok,
						satuan : satuan
					},
					success:function(data){
						swal_success(data.msg);
						$("#formQuickAddBarang").trigger('reset');
						$("#modalAddBarang").fadeOut('slow');
						setTimeout(function(){window.location.reload();},1000);
					},
					error:function(data){
						swal_failed(data.msg);
						$("#formQuickAddBarang").trigger('reset');
					}
				});
			}
		}else{

		}
 	})