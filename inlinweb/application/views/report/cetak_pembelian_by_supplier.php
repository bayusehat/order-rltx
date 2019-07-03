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
		<th><strong>NAMA SUPPLIER</strong></th>
	</tr>
	<tr>
		<td>Jl.Darmo Permai III Bavarian B-28,Surabaya</td>
		<td><?php 
			$id = $this->uri->segment(3);
			$query = $this->db->query('SELECT * FROM tb_supplier WHERE id_supplier='.$id)->row();
			echo $query->nama_supplier;
		?></td>
	</tr>
</table>
<br>
<table border="1" style="border-collapse: collapse;width: 100%" id="tb-detail">
	<tr>
		<th>Nomor</th>
		<th>Tanggal</th>
		<th>Payment</th>
		<th>Total</th>
	</tr>
	<?php
	$total = 0;
		foreach ($cetak as $data) {
			echo'
			<tr>
				<td>'.$data->nomor_pembelian.'</td>
				<td>'.$data->tanggal_pembelian.'</td>
				<td>'.$data->nama_payment.'</td>
				<td>Rp '.number_format($data->total).'</td>
			</tr>';
			$total+=$data->total;
		}
		echo '<tr>
				<td colspan="3"><b>Grand Total : </b></td>
				<td>Rp '.number_format($total).'</td>
			</tr>';
	?>
</table>

