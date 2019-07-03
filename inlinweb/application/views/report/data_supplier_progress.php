<?php $this->load->view('head_dash');?>
<div id="content-wrapper" class="group">
	<div id="page-wrapper">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="card">
				   	<h3> Data Supplier Progress </h3>
				    	<hr>
				    	<div class="row">
							<div class="col-md-12 col-sm-12">
								<table id="tableRetur" class="table table-striped table-condensed table-bordered nowrap" style="width:100%">
						            <thead>
						                <tr>
						                 	<th>No. </th>
						                    <th>Nama Supplier</th>
						                    <th>Banyak Pembelian</th>
						                    <th>Total Pembelian</th>
						                    <th>Detail</th>
						                </tr>
						            </thead>
						            <tbody>
						            	<?php
						            	$no = 1;
						            		foreach ($supplier as $data) {
						            			if($data->deleted == 0){
						            			echo'
						            			<tr>
						            				<td>'.$no++.'</td>
						            				<td>'.$data->nama_supplier.'</td>
						            				<td>'.$data->jumlah_pembelian.'</td>
						            				<td>Rp '.number_format($data->total_beli).'</td>
						            				<td>
						            					<a href="'.base_url().'index.php/admin/pembelian/'.$data->id_supplier.'" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat Pembelian</a>
						            					<a href="'.base_url().'index.php/admin/cetak_pembelian_by_supplier/'.$data->id_supplier.'" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Cetak Laporan Pembelian</a>
						            				</td>
						            			</tr>
						            			';
						            			}
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
	</div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/DataTables/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
<script src="<?php echo base_url();?>assets/DataTables/Bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/DataTables/datatables.min.js"></script>
<script>
   $.noConflict();
        jQuery(document).ready(function ($) {
            // $("#tgl_retur").datepicker({dateFormat: 'yy-mm-dd'});
            $('#tableRetur').DataTable({
	              "lengthMenu": [[5, 10, 50, -1], [5, 10, 25, "All"]]
	            });
        });
    </script>