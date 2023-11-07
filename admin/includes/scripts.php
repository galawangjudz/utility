	<script src="../vendors/scripts/core.js"></script>
	<script src="../vendors/scripts/script.min.js"></script>
	<script src="../vendors/scripts/pre_loader.js"></script>
	<script src="../vendors/scripts/process.js"></script>
	<script src="../vendors/scripts/layout-settings.js"></script>
	<!-- <script src="../src/plugins/apexcharts/apexcharts.min.js"></script> -->
	<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
	<!-- <script src="../vendors/scripts/datagraph.js"></script> -->

	<!-- buttons for Export datatable -->
	<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
	<script src="../src/plugins/datatables/js/pdfmake.min.js"></script>
	<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
	
<!-- 	<script src="../vendors/scripts/advanced-components.js"></script> -->
<script>
function check_session_id()
  {
    var session_id = "<?php echo $_SESSION['user_session_id']; ?>";
    fetch('../includes/session.php').then(function(response){
        return response.json();
    }).then(function(responseData){
        if(responseData.output == 'logout')
        {
			window.location = "../logout.php";
        }
    }); 
  }  
  setInterval(function() {
        check_session_id()
  }, 10000);

  </script>