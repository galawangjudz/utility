function start_loader(){
	$('body').prepend('<div id="preloader"></div>')
}
function end_loader(){
	 $('#preloader').fadeOut('fast', function() {
        $(this).remove();
      })
}

// function 
window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
  var Toast = Swal.mixin({
      toast: true,
      position: $pos || 'top',
      showConfirmButton: false,
      timer: 3500
  });
  Toast.fire({
      icon: $bg,
      title: $msg
  })
}
