$(document).ready(function(){
    $("#username").focus();
});

 $('input').keypress(function(e) {
    if(e.which == 13) { 
        $('#btn').trigger('click');
    }
});

$("#btn").click(function(){
    var username = $("#username").val();
    var password = $("#password").val();
    var url_admin= base_url+"index.php/admin";

    $.ajax({
        type:"POST",
        url:base_url+"index.php/admin/login",
        dataType:"json",
        data:{
            username : username,
            password : password
        },
        success:function(data){
            if(data > 0){
                $("#berhasil").slideDown('slow');
                setTimeout(function(){$("#berhasil").slideUp('slow', function(){
                    window.location = url_admin;
                });},1000);
            }else{
                 $("#gagal").slideDown('slow');
                setTimeout(function(){$("#gagal").slideUp('slow', function(){
                });},1000);
                $('form').trigger('reset');
                $("#username").focus();
            }
        }
    });
});

    $("#checkPassword").click(function(){
        if($("#password").attr('type') === "password"){
            $("#password").attr('type','text');
        }else{
            $("#password").attr('type','password');
        }
    });