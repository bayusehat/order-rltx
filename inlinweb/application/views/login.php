<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Inlin Jaya Variasi Login Admin</title>
        <link rel="icon" href="<?php echo base_url();?>assets/spada/images/icon.png" type="image/png" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/fontawesome-all.min.css" type="text/css" media="screen">
        <link href="https://fonts.googleapis.com/css?family=Work+Sans" rel="stylesheet">
        <link href="<?php echo base_url();?>assets/spada/css/login.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url();?>assets/spada/css/custom-fonts.css" type="text/css" media="screen">
        <script>
            var base_url = "<?php echo base_url();?>";
        </script>
    </head>
        <body>
            <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm 4">
                    <img src="<?php echo base_url();?>assets/spada/images/logo.png" alt="logo" class="img-responsive"/>
                    <br>
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" style="text-align:center">Inlin Jaya Variasi Login Admin</h3>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-success center" id="berhasil"><i class="fa fa-check-circle"></i> Login berhasil!</div>
                                <div class="alert alert-danger center" id="gagal"><i class="fa fa-times-circle"></i> Username atau Password Salah!</div>
                            <!-- Form -->
                            <form>
                                <fieldset>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default" type="button"><i class="fa fa-user"></i></a>
                                            </span>
                                                <input class="form-control" placeholder="Username"  name="username" type="text" id="username">
                                            </div>
                                        </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default" type="button"><i class="fa fa-lock"></i></a>
                                            </span>
                                        <input class="form-control" placeholder="Password" name="password" id="password" type="password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <input type="checkbox" id="checkPassword"> Show Password
                                        </div>         
                                    <input type="button" name="submit" id="btn" class="btn btn-block btn-success" value="Login">
                                </fieldset>
                            </form>
                        </div>
                        <div class="panel-footer center">
                            2019 &copy; SPADA Digital Consulting - spada.id
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/spada/js/login.js'?>"></script>