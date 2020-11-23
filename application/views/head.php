<?php
	header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	function auto_version($file='')
	{
		if($file == '')
			return $file;
		else
			$pathfile = FCPATH.$file;
		
	    if(!file_exists($pathfile))
	        return $file;
	    //MAKE SURE FILE NAME IS CORRECT!
	 
	    $mtime = filemtime($pathfile);
	    return $file.'?'.$mtime;
	}
?><!DOCTYPE HTML>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
<head>
    <?php
    foreach($css_files as $file): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
    <?php endforeach; ?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,target-densitydpi=device-dpi, user-scalable=no" /> -->
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/spada/images/icon-rltx.png">
    <meta name="description" content="Backend UI" />
    <meta name="author" content="SPADA Digital Consulting" />
	<title><?php echo $title;?> - RLTX Store</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/bootstrap-3.3.7.min.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/normalize.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/grid.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo base_url().auto_version('assets/spada/css/style.css'); ?>" type="text/css" media="screen">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/modal-center.css" type="text/css" media="screen">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/add-css.css" type="text/css" media="screen">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/custom-fonts.css" type="text/css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/spada/css/float-button.css">
    <script src="<?php echo base_url();?>assets/spada/js/sweetalert/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/sweetalert2.min.css">
    <script src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Work+Sans" rel="stylesheet">
    <script>
        var base_url = '<?php echo base_url() ?>';
    </script>
</head>
<body>
    <?php if ($this->session->flashdata('berhasil')): ?>
        <script>
            swal({
                title: "Berhasil",
                text: "<?php echo $this->session->flashdata('berhasil'); ?>",
                timer: 2500,
                showConfirmButton: false,
                type: 'success'
                });
        </script>
    <?php endif; ?>
    <?php if ($this->session->flashdata('gagal')): ?>
        <script>
            swal({
                title: "Gagal",
                text: "<?php echo $this->session->flashdata('gagal'); ?>",
                timer: 2500,
                showConfirmButton: false,
                type: 'error'
                });
        </script>
    <?php endif; ?>
    <div id="main" class="group">
        <div id="left-panel" class="col">
            <div id="logo">
                <img src="<?php echo base_url();?>assets/spada/images/logo-rltx.png">
            </div>
            <div id="left-navigation">
                <ul class="main-menu">
                    <li class="menu-item">
                        <a href="<?php echo base_url();?>index.php/admin"><i class="fa fa-dashboard"></i>Dashboard</a>
                    </li>
                    <?php 
                    if($this->session->userdata('level') == 'admin') {
                        echo'
                        <li class="menu-item">
                            <a href="javascript:void(0)"><i class="fa fa-database"></i>Data Master</a>
                            <ul class="sub-menu">
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/barang">Data Barang</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/kategori_barang">Data Kategori Barang</a>
                                </li>
                                 <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/pelanggan">Data Pelanggan</a>
                                </li>
                                 <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/sales">Data Sales</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/user">Data User</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/supplier">Data Supplier</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/payment">Data Payment</a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item">
                            <a href="javascript:void(0)"><i class="fa fa-database"></i>Inventory</a>
                            <ul class="sub-menu">
                            	<li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/stock_opname">Mutasi Barang</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/stock_opname_baru">Stock Opname</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="'.base_url().'index.php/admin/stock_opname_prep">Print Stock Sheet</a>
                                </li>
                            </ul>
                        </li>
                        ';
                     } ?>
                    <li class="menu-item">
                        <a href="javascript:void(0)"><i class="fa fa-shopping-cart"></i>Transaction</a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/penjualan">Penjualan</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/pembelian">Pembelian</a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                            <a href="javascript:void(0)"><i class="fa fa-database"></i>Giro</a>
                            <ul class="sub-menu">    
                            	<li class="sub-menu-item">
                                    <a href="<?= base_url();?>index.php/admin/giro_masuk">Data Giro Masuk</a>
                                </li>
                                <li class="sub-menu-item">
                                    <a href="<?= base_url();?>index.php/admin/giro_keluar">Data Giro Keluar</a>
                                </li>
                            </ul>
                        </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)"><i class="fa fa-file"></i>Report</a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/piutang">Data Piutang</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/hutang">Data Hutang</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/laporan_penjualan">Laporan Penjualan</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/data_supplier_progress">Pendapatan Supplier</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/data_sales_progress">Omset Sales</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/data_pelanggan_progress">Omset Pelanggan</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/history_stok">History Stok</a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="<?php echo base_url();?>index.php/admin/history_harga">History Harga</a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)"><i class="fa fa-cog"></i>Setting</a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="#" data-toggle="modal" data-target="#modalChange">Change Password</a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo base_url();?>index.php/admin/logout" style="color: red" onclick="return confirm('Apakah Anda yakin ingin keluar?');"><i class="fa fa-sign-out"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        <div id="content" class="group">
            <div id="top-panel">
                        <div class="top-wrapper">
                            <div id="page-title" class="left">
                                <strong><h1><?php echo $title;?> - <?php echo $this->session->userdata('nama_user');?> </h1></strong>
                            </div>
                            <div id="user-account" class="right">
                                
                            </div>
                                <div id="notification" class="right">
                                    <div class="dropdown">
                                        <i class="fa fa-bell dropdown-toggle" data-toggle="dropdown"></i>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li class="dropdown-header">Nota Penjualan</li>
                                                <?php
                                               $penjualan =  $this->db->query("SELECT * FROM tb_penjualan WHERE status='Belum Terbayar' OR tanggal_jatuh_tempo < CURRENT_TIMESTAMP ORDER BY id_penjualan DESC LIMIT 5")->result();
                                                    foreach ($penjualan as $data) {
                                                        if($data->tanggal_jatuh_tempo < date('Y-m-d H:i:s')){
                                                            $tag = 'Jatuh tempo';
                                                        }else{
                                                            $tag = $data->status;
                                                        }
                                                       echo '<li><a href="'.base_url().'index.php/admin/detail_penjualan/'.$data->id_penjualan.'">'.$data->nomor_penjualan.' - '.$tag.'</a></li>';
                                                    }
                                                ?>
                                                
                                                <li class="dropdown-header">Nota Pembelian</li>
                                                <?php
                                                 $pembelian =  $this->db->query("SELECT * FROM tb_pembelian WHERE status='Belum Terbayar' OR tanggal_jatuh_tempo < CURRENT_TIMESTAMP ORDER BY id_pembelian DESC LIMIT 5")->result();
                                                    foreach ($pembelian as $data) {
                                                        if($data->tanggal_jatuh_tempo < date('Y-m-d H:i:s')){
                                                            $tag = 'Jatuh tempo';
                                                        }else{
                                                            $tag = $data->status;
                                                        }
                                                        echo ' <li><a href="'.base_url().'index.php/admin/detail_pembelian/'.$data->id_pembelian.'">'.$data->nomor_pembelian.' - '.$tag.'</a></li>';
                                                    }
                                                  ?>

                                                <li class="dropdown-header">Giro Masuk</li>
                                                <?php
                                                 $giro_masuk =  $this->db->query("SELECT * FROM tb_giro_masuk WHERE status_giro='Pending' OR tanggal_jatuh_tempo < CURRENT_TIMESTAMP ORDER BY id_giro_masuk DESC LIMIT 5")->result();
                                                    foreach ($giro_masuk as $data) {
                                                        if($data->tanggal_jatuh_tempo < date('Y-m-d H:i:s')){
                                                            $tag = 'Jatuh tempo';
                                                        }else{
                                                            $tag = $data->status_giro;
                                                        }
                                                        echo ' <li><a href="'.base_url().'index.php/admin/giro_masuk/edit/'.$data->id_giro_masuk.'">'.$data->no_giro.' - '.$tag.'</a></li>';
                                                    }
                                                  ?>

                                                <li class="dropdown-header">Giro Keluar</li>
                                                <?php
                                                 $giro_keluar =  $this->db->query("SELECT * FROM tb_giro_keluar WHERE status='Pending' OR tanggal_jatuh_tempo < CURRENT_TIMESTAMP ORDER BY id_giro_keluar DESC LIMIT 5")->result();
                                                    foreach ($giro_keluar as $data) {
                                                        if($data->tanggal_jatuh_tempo < date('Y-m-d H:i:s')){
                                                            $tag = 'Jatuh tempo';
                                                        }else{
                                                            $tag = $data->status;
                                                        }
                                                        echo ' <li><a href="'.base_url().'index.php/admin/giro_keluar/edit/'.$data->id_giro_keluar.'">'.$data->no_giro.' - '.$tag.'</a></li>';
                                                    }
                                                  ?>
                                          </ul>
                                    </div>
                                </div>
                            
                            
                            <!-- <div id="search-panel" class="right">
                                <form>
                                    <input type="text" name="search" placeholder="Search">
                                    <span>
                                        <input type="button" value="">
                                        <i class="fa fa-search"></i>
                                    </span>
                                </form>
                            </div> -->
                        </div>
                    </div>
                    <!-- modal Change Password -->
                        <form method="post">
                            <div class="modal fade" id="modalChange" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6">
                                                    <h4 class="modal-title">Change Password <?php echo $this->session->userdata('nama_user');?></h4>
                                                </div>
                                            <div class="col-md-6 col-sm-6">
                                                <button type="button" class="btn btn-danger right btn-sm" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                  <div class="modal-body">
                                        <div class="alert alert-success" id="berhasil_ubah"><i class="fa fa-check-circle"></i> Password berhasil diubah</div>
                                        <div class="alert alert-danger" id="gagal_ubah"><i class="fa fa-times-circle"></i> Password gagal diubah</div>
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm New Password</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" onkeyup="valid()">
                                            <span id="notif"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block" onclick="changePassword(); return false;"><i class="fa fa-edit"></i> Update Password</button>
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </form>
