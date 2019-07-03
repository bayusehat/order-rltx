<?php $this->load->view('head_dash');?>
		<div id="content-wrapper" class="group">
			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="panel panel-info">
			            <div class="panel-heading">Edit User</div>
				            <form method="post" action="<?php echo base_url();?>index.php/admin/edit_user/<?php echo $this->uri->segment(3);?>">
				            	<div class="panel-body">
				            		<div class="form-group">
				            			<label>Nama</label>
				            			<input type="hidden" name="id_user" class="form-control" id="nama" value="<?php echo $edit->id_user;?>">
				            			<input type="text" name="nama_user" class="form-control" id="nama" value="<?php echo $edit->nama;?>">
				            		</div>
				            		<div class="form-group">
				            			<label>Username</label>
				            			<input type="text" name="username" class="form-control" id="username" value="<?php echo $edit->username;?>">
				            		</div>
				            		<div class="form-group">
				            			<label>Password</label>
				            			<input type="password" name="password" class="form-control" id="password">
				            		</div>
				            		<div class="form-group">
				            			<label>Confirm Password</label>
				            			<input type="password" name="confirm_password" class="form-control" id="confirm_password">
				            			<span id="notif"></span>
				            		</div>
				            		<div class="form-group">
				            			<label>E-mail</label>
				            			<input type="email" name="email" class="form-control" id="email" value="<?php echo $edit->email;?>">
				            		</div>
				            		<?php
				            		if($this->session->userdata('level') == 'admin'){
				            			echo'
				            		<div class="form-group">
				            			<label>level</label>
				            			<select name="level" class="form-control" id="level">
				            				<option value="'.$edit->level.'">'.$edit->level.'</option>
				            				<option value="admin">admin</option>
				            				<option value="user">user</option>
				            			</select>
				            		</div>';
				            			}
				            		?>
							<div class="panel-footer">
								<input type="submit" name="submit" class="btn btn-success btn-block btn-new" value="Update User">
								</div>
				            </form>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript">

	$("#confirm_password").keyup(function(){
		if($("#password").val() != $("#confirm_password").val()){
			$("#notif").html('Not Matching!').css('color','red');
		}else{
			$("#notif").html('Matching!').css('color','green');
		}
	});
	
</script>