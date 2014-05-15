function AjaxQuickSearch(userType){
    var _self = this;

    this.Option = {
        //url: '/admin/user/search/',
        container: '.quick-result',
        input: '#u-search'
    }
    
    if(userType == 0) this.Option.url = '/admin/user/search/';
    else if(userType == 1) this.Option.url = '/admin/contact/search/';
    else this.Option.url = '/admin/changes/search/';
    $(_self.Option.input).keyup(function(){
        if($.trim($(this).val())) {
            /*if($('.quick-result').hasClass('hide')) {
                $('.quick-result').removeClass('hide');
                //$('.quick-result').fadeIn(200);
            }*/
            $('.quick-result').fadeIn(200);
            _self.AjaxRequest($(this).val());
        } else {
            $('.quick-result').fadeOut(200);
            //$('.quick-result').addClass('hide');
        }
    })

    this.AjaxRequest = function(query){
        $.ajax({
            'url': _self.Option.url,
            'data': {q: query, ajax: true},
            'success': function(html){_self.AjaxSuccess(html);}
        })
    }

    this.AjaxSuccess = function(html){
         $(_self.Option.container).html(html);
    }
}