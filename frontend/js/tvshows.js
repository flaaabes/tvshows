function ajaxLinks(){
    $('.remote_link').click(function(){
        var unixTS = Math.round(+new Date()/1000);
        var method = $(this).data('method');
        var params = $(this).data('params');
        var _this  = this
        $(this).after('<img class="link_' + unixTS + '" style="margin: 0px; padding: 0px; margin-left: 10px;" src="/tvshows/frontend/images/ajax-loader.gif">');
        var res = $.ajax({
          url: "/tvshows/lib/ajax/ajax.php",
          global: false, 
          type: "POST", 
          data: params, 
          cache: false,
          success: function(){ $(_this).remove(); }
        });
    });
}

function ajaxUpdate(){
    $('.remote_update').click(function(){
      var query  = $('#add_searchShow').val();
      var url    = $(this).data('url');
      var update = $(this).data('update');
      var partial = $(this).data('partial');
      var _this = this;
      var response = '';
      $(this).val('searching...');
      $(this).attr('disabled','disabled');

      var res = $.ajax({
        url: "/tvshows/lib/ajax/" + url,
        global: false, 
        type: "POST", 
        cache: false,
        data: {q:query,p:partial},
        success: function(response){  
            $(_this).val('search'); 
            $(_this).removeAttr('disabled');
            $('#' + update).html(response)
        }
      });
    });
}