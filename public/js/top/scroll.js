
var startPos = 0;
var winScrollTop = 0;

$(window).on('scroll',function(){
    winScrollTop = $(this).scrollTop();
    if (winScrollTop >= startPos) {
        if(winScrollTop >= 200){
            $('.site-header').addClass('hide');
        }
    } else {
        $('.site-header').removeClass('hide');
    }
    startPos = winScrollTop;
});