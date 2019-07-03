 </div>
    </div>
     <!-- FLoating Button -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/spada/js/jquery-1.9.1.min.js"></script>
	<script src="<?php echo base_url();?>assets/DataTables/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
  	<script src="<?php echo base_url();?>assets/spada/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>
	<?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#password").focus();
            
            $('.fa-print').parent().attr("target", "_blank");
        });
        
        function changePassword() {
            
            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();

            if(password == "" && confirm_password== ""){
                $('#gagal_ubah').slideDown('slow');
                setTimeout(function(){$("#gagal_ubah").slideUp('slow')},1000);
            }else{

            $.ajax({
                type : "POST",
                url : "<?php echo site_url('admin/change_password');?>",
                dataType : "json",
                data : {
                    password : password,
                    confirm_password : confirm_password
                },
                success:function(data){
                    console.log(data);
                    $('[name="password"]').val("");
                    $('[name="confirm_password"]').val("");
                    $('#berhasil_ubah').slideDown();
                    setTimeout(function(){$("#berhasil_ubah").slideUp('slow', function(){
                        window.location.reload();
                    });},1000);
                },
                error:function(error){
                    $('[name="password"]').val("");
                    $('[name="confirm_password"]').val("");
                    $('#gagal_ubah').slideDown('slow');
                    $("#notif").hide();
                    setTimeout(function(){$("#gagal_ubah").slideUp('slow');},1000);
                }
            });
        }
            return false;
        }

        function valid() {
            if($("#password").val() != $("#confirm_password").val()){
                $("#notif").html('Not Matching!').css('color','red');
            }else{
                $("#notif").html('Matching!').css('color','green');
            }
        }
    </script>
</body>
</html>