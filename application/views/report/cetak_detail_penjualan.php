<style type="text/css">
	.t-right{
		text-align: right;
	}
	.td-pad{
		padding: 10px;
	}
	#tb-detail td{
		padding: 10px;
	}
	.right{
		text-align: right;
	}
</style>
<table width="100%">
	<tr>
		<th><strong></strong>Inlin Jaya Variasi</strong></th>
		<th><strong>Detail :</strong></th>
	</tr>
	<tr>
		<td>Penjualan : <?= $detail->nomor_penjualan;?></td>
		<td>Pelanggan : <?= $detail->nama_pelanggan;?></td>
	</tr>
	<tr>
		<td>Tanggal : <?= date('d F Y H:i',strtotime($detail->tanggal_penjualan)); ?></td>
		<td>Jatuh Tempo : <?php
			if($detail->status == "Lunas"){
				echo " - ";
			}else{
				echo date('d F Y',strtotime($detail->tanggal_jatuh_tempo));
			}
			
		;?> </td>
	</tr>
	<tr>
		<td>Payment : <?= $detail->nama_payment;?></td>
		<td>Cash : <?= $detail->cash_payment;?></td>
	</tr>
	<tr>
		<td>Status : <?= $detail->status;?></td>
		<td>Tanggal Lunas : 
		<?php
			if($detail->status != 'Lunas'){
				echo '-';
			}else{
				echo date('d F Y',strtotime($detail->tanggal_lunas));
			}
		?>
		</td>
	</tr>
	<tr>
		<td>Sales : <?= $detail->nama_sales;?></td>
		<td>User : <?= $detail->username;?></td>
	</tr>
</table>
<br>
<table border="1" style="border-collapse: collapse;width: 100%" id="tb-detail">
	<tr>
		<th>Nama barang</th>
		<th>Harga</th>
		<th>Diskon</th>
		<th>Quantity</th>
		<th>Subtotal</th>
	</tr>
	<?php
	$cetak = $this->db->query('SELECT * FROM tb_detail_penjualan WHERE id_penjualan='.$detail->id_penjualan)->result();
		foreach ($cetak as $data) {
			if($data->subtotal != '0'){
				echo'
				<tr>
					<td>'.$data->nama_barang.'</td>
					<td>Rp '.number_format($data->harga).'</td>
					<td>'.$data->diskon.'%</td>
					<td>'.$data->jumlah.'</td>
					<td>Rp '.number_format($data->subtotal).'</td>
				</tr>';
			}	
		}
		echo '
			<!--<tr>
				<td colspan="4" class="right"><b>BAYAR</b></td>
				<td>Rp '.number_format($detail->bayar).'</td>
			</tr>
			<tr>
				<td colspan="4" class="right"><b>SISA </b></td>
				<td>Rp '.number_format($detail->sisa).'</td>
			</tr>
			<tr>
				<td colspan="4" class="right"><b>TOTAL RETUR</b></td>
				<td>Rp '.number_format($detail->total_retur_penjualan).'</td>
			</tr>-->
			<tr>
				<td colspan="4" class="right"><b>PPN</b></td>
				<td>Rp '.number_format($detail->nominal_ppn).'</td>
			</tr>
			<tr>
				<td colspan="4" class="right"><b>TOTAL</b></td>
				<td>Rp '.number_format($detail->total).'</td>
			</tr>';
	?>