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
</style>
<table width="100%">
	<tr>
		<th><strong></strong>SPADA Store</strong></th>
		<th><strong>BUKTI RETUR</strong></th>
	</tr>
	<tr>
		<td>Jl.Darmo Permai III Bavarian B-28</td>
		<td>Nomor Retur</td>
		<td>:</td>
		<td><?php echo $cetak_retur->nomor_retur_penjualan;?></td>
	</tr>
	<tr>
		<td>No. 2 Surabaya ,0895364791</td>
		<td>Tanggal Retur</td>
		<td>:</td>
		<td><?php echo date('d F Y',strtotime($cetak_retur->tanggal_retur_penjualan));?></td>
	</tr>
	<tr>
		<td></td>
		<td>Nomor Penjualan  </td>
		<td>:</td>
		<td><?php echo $cetak_retur->nomor_penjualan;?></td>
	</tr>
	<tr>
		<td></td>
		<td>Pelanggan </td>
		<td>:</td>
		<td><?php echo $cetak_retur->nama_pelanggan;?></td>
	</tr>
</table>
<br>
<table border="1" style="border-collapse: collapse;width: 100%" id="tb-detail">
	<tr>
		<th>SKU</th>
		<th>Nama Barang</th>
		<th>Qty. Jual</th>
		<th>Qty. Retur</th>
		<th>Harga</th>
		<th>Subtotal</th>
	</tr>
	<?php
		$id = $this->uri->segment(3);
		$query = $this->db->query('SELECT * FROM tb_detail_retur_penjualan WHERE id_retur_penjualan='.$id)->result();

		foreach ($query as $data) {
			echo'
			<tr>
				<td>'.$data->sku_barang.'</td>
				<td>'.$data->nama_barang.'</td>
				<td>'.$data->jumlah_jual.'</td>
				<td>'.$data->jumlah_retur.'</td>
				<td>Rp '.number_format($data->harga).'</td>
				<td>Rp '.number_format($data->subtotal_retur).'</td>
			</tr>';
		}
	?>

</table>
<br>
<table width="100%">
	
	<tr>
		<td>User : <?php echo $this->session->userdata('nama_user');?></td>
		<td class="t-right">Keterangan</td>
		<td>:</td>
		<td class="t-right"><?php echo $cetak_retur->keterangan;?></td>
	</tr>
	<tr>
		<td ></td>
		<td class="t-right">Subtotal</td>
		<td>:</td>
		<td class="t-right"><?php echo 'Rp '.number_format($cetak_retur->subtotal_retur);?></td>
	</tr>
	<tr>
		<td></td>
		<td class="t-right">Pajak</td>
		<td>:</td>
		<td class="t-right">0.00%</td>
	</tr>
	<tr>
		<td></td>
		<td class="t-right"><strong>Total</strong></td>
		<td>:</td>
		<td class="t-right"><strong><?php echo 'Rp '.number_format($cetak_retur->total_retur);?></strong></td>
	</tr>
</table>

