jQuery(function($){

    var inputSelectors = 'input[type=text],input[type=url]';
    var titleShorten = 'Click to shorten this URL';
    var titleShortenUndo = 'Click to undo URL shortening';
    var updateFieldClass = function(e, item){

        var $field = $(e.target);

        if($('body').hasClass('cusmin-url-shortener-disable-field') && !$field.hasClass('ab-cusmin-input')){
            return false;
        }

        if(item){
            $field = $(this);
        }

        var val = $field.val();
        $field.addClass('cusmin-url-shortener');

        var $nextElement = $field.next();
        if($nextElement.hasClass('cusmin-url-shortener-btn')){
            $nextElement.remove();
        }
        if(val && (val.trim().indexOf('http://') == 0 || val.trim().indexOf('https://') == 0)){
            var mt = ($field.height()/2) - 3;
            var fmt = $field.css('margin-top').replace('px','');
            mt += parseInt(fmt);
            var classes = 'cusmin-url-shortener-btn';
            var title = titleShorten;
            if(val.indexOf('goo.gl') !== -1){
                classes +=' shortened';
                title = titleShortenUndo;
            }
            $field.after('<div class="'+classes+'" title="'+title+'" style="margin-top:'+mt+'px"></div>');
            $field.addClass('cusmin-url-shortener');
        }
    };
    var expandOrShorten = function(url, action, callback, onError){
        var nonce = $('#cusmin_us_short_nonce').val();

        $.ajax({
            url: ajaxurl,
            type : 'post',
            data: {
                'action':action,
                'url' : url,
                'nonce': nonce
            },
            success:function(data) {
                if(data.status == 'success'){
                    if(callback){
                        callback(data.url);
                    }
                }else{
                    console.log('err', data);
                    if(onError){
                        onError(data.message);
                    }
                }
            },
            error: function(errorThrown){
                console.log('unhandled error', errorThrown);
            }
        });
    }
    var shortenUrl = function(url, callback, onError){
        expandOrShorten(url, 'cusmin_us_short', callback, onError);
    };
    var expandUrl = function(url, callback, onError){
        expandOrShorten(url, 'cusmin_us_expand', callback, onError);
    };
    var copyToClipboard = function(){
        document.execCommand("copy");
    }

  $(document)
  .change(inputSelectors, updateFieldClass)
  .on('click', '#wpadminbar .cusmin-go-shorten', function(e){
    e.preventDefault();
          var $input = $(e.target).parent().find('input:first');
          var $wrap = $('#wp-admin-bar-cusmin-us-ab');
          var val = $input.val();
          $input.data('val', val);
          $wrap.addClass('loading');
          shortenUrl(val,
              function(shortened){
                  $input.val(shortened).select();
                  $wrap.removeClass('loading');
                  copyToClipboard();
                },
              function(error){
                  $input.val(error);
                  $wrap.addClass('error');
                  $wrap.removeClass('loading');
                  setTimeout(function(){
                      $input.val($input.data('val'));
                      $wrap.removeClass('error');
                  }, 2000);
              }
          );
  })
  .on('click', '.cusmin-url-shortener-btn', function(e){
         e.preventDefault();
         var $btn = $(e.target);
         var $input = $btn.prev();
          $btn.addClass('loading');
          if($input.hasClass('cusmin-url-shortener')){
              var val = $input.val();
              $input.data('val', $input.val());
              if(val.indexOf('goo.gl') !== -1){
                  expandUrl(val, function(expanded){
                      $btn.removeClass('loading');
                      $btn.removeClass('shortened');
                      $btn.attr('title', titleShorten);
                      $input.val(expanded).select();
                      copyToClipboard();
                  },
                      function(error){
                          $btn.removeClass('loading');
                          $input.val(error);
                          $input.addClass('error');
                          setTimeout(function(){
                              $input.val($input.data('val'));
                              $input.removeClass('error');
                          }, 2000);
                      });
              }else{
                  shortenUrl(val, function(shortened){
                      $btn.removeClass('loading');
                      $btn.addClass('shortened');
                      $btn.attr('title', titleShortenUndo);
                      $input.val(shortened).select();
                      copyToClipboard();
                  },function(error){
                      $btn.removeClass('loading');
                      $input.val(error);
                      $input.addClass('error');
                      setTimeout(function(){
                          $input.val($input.data('val'));
                          $input.removeClass('error');
                      }, 2000);
                  });
              }

          }
  })
  .on('keyup', '.cusmin-url-shortener', function(e){
          if(e.keyCode == 13)
          {
              $('#wpadminbar .cusmin-go-shorten').trigger('click');
          }
  })
  ;

  //prepare on load
  $(inputSelectors).each(updateFieldClass);


});
