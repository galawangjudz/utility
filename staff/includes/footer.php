<script>
  $(document).ready(function(){
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-lg modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window.uni_modal_ticket = function($title = '', $url = '', $size = "", hideFooter = false) {
        start_loader();
        $.ajax({
            url: $url,
            error: err => {
                console.log();
                alert("An error occurred");
            },
            success: function (resp) {
                if (resp) {
                    $('#uni_modal_ticket .modal-title').html($title);
                    $('#uni_modal_ticket .modal-body').html(resp);

                    // Conditionally hide the footer
                    if (hideFooter) {
                        $('#uni_modal_ticket .modal-footer').addClass('hidden-footer');
                    } else {
                        $('#uni_modal_ticket .modal-footer').removeClass('hidden-footer');
                    }

                    if ($size != '') {
                        $('#uni_modal_ticket .modal-dialog').addClass($size + ' modal-dialog-centered');
                    } else {
                        $('#uni_modal_ticket .modal-dialog').removeAttr("class").addClass("modal-dialog modal-lg modal-dialog-centered");
                    }
                    $('#uni_modal_ticket').modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false,
                        focus: true
                    });
                    end_loader();
                }
            }
        });
    };

   
    window.uni_modal_2 = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal_2 .modal-title').html($title)
                    $('#uni_modal_2 .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal_2 .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal_2 .modal-dialog').removeAttr("class").addClass("modal-dialog modal-lg modal-dialog-centered")
                    }
                    $('#uni_modal_2').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }

    window.uni_modal_payment = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal_payment .modal-title').html($title)
                    $('#uni_modal_payment .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal_payment .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal_payment .modal-dialog').removeAttr("class").addClass("modal-dialog modal-lg modal-dialog-centered")
                    }
                    $('#uni_modal_payment').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }

    window.uni_modal_right = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal_right .modal-title').html($title)
                    $('#uni_modal_right .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal_right .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal_right .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal_right').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }


    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>

<!-- 
<div class="main_container">
    <div class="footer-wrap pd-20 mb-20 card-box">
                    ALSC Utility System <a href="https://asianland.ph/" target="_blank"><span>developed by </span> Jude Dela Cruz</a>
    </div>
</div> -->

