<?php $this->load->view('head_dash');?>
<div id="content-wrapper" class="group">
	<div id="page-wrapper">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="card">
				  <div class="row">
				   	<div class="col-md-8 col-sm-8">
				   		<div class="tgl">
			                    <form class="form-inline">
			                        <div class="form-group">
			                            <label>Dari Tanggal : </label>
			                            <input type="text" name="from_date" id="from_date" class="form-control input-sm datepicker">
			                        </div>
			                        <div class="form-group">
			                            <label>s/d.</label>
			                            <input type="text" name="to_date" id="to_date" class="form-control input-sm datepicker">
			                        </div>
			                        <div class="form-group">
			                            <button type="button" id="btn-seacrh" class="btn btn-primary btn-sm">Cari</button>
			                        </div>
			                        <div class="form-group">
			                            <button type="button" id="btnrefresh" class="btn btn-warning btn-sm"><i class="fa fa-sync"></i></button>
			                        </div>
			                    </form> 
                			</div>
				   		   </div>
				   		<div class="col-md-4 col-sm-4">
				   			<button id="btnPrint" class="btn btn-primary" style="float: right;"><i class="fa fa-print"></i> Cetak</button>
				   		</div>
				   	</div>
				    	<hr>
				    	<div class="row" id="printTable">
							<div class="col-md-12 col-sm-12">
								<div class="scroll-laporan" id="detail_laporan" style="border: none">

								</div>
							</div>
						</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('foot_dash');?>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/jquery-2.2.3.min.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/bootstrap/js/bootstrap.js'?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script>
   $.noConflict();
        jQuery(document).ready(function ($) {
            $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
            $('.tbltgl').DataTable({
	              "lengthMenu": [[5, 10, 50, -1], [5, 10, 25, "All"]]
	            });
        });

    function printData()
    {
       var divToPrint=document.getElementById("printTable");
       newWin= window.open("");
       newWin.document.write(divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    }

    $('#btnPrint').on('click',function(){
    printData();
    });

    $(document).ready(function(){  
           $('#btn-seacrh').click(function(){  
                var from_date = $('#from_date').val();  
                var to_date = $('#to_date').val();
                  
                if(from_date != '' && to_date != '')  
                {  
                     $.ajax({  
                          url:"<?php echo base_url();?>index.php/admin/cari_laporan",  
                          method:"POST",  
                          data:{from_date:from_date, to_date:to_date},  
                          success:function(data)  
                          {  
                            $('#detail_laporan').html(data);
                            // setTimeout(function(){$("#preload").slideUp('slow', function(){
                            //     $('#detail_laporan').html(data);
                            // });},2000);
                          }  
                     });  
                }  
                else  
                {  
                    alert("Please Select Date");  
                    $("#from_date").focus();
                }  
           });

           $("#btnrefresh").click(function(){
	           	$('#detail_laporan').empty();
	           	$('.datepicker').val("");
           });  
      });
    </script>