/*=========================================================================================
  File Name: footer.js
  Description: Template footer js.
  ----------------------------------------------------------------------------------------
  Item Name: SMS Marketing - Bulk SMS Application For Marketing
  Author: Codeglen
  Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

//Check to see if the window is top if not then display button
$(document).ready(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 400) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    });

    //Click event to scroll to top
    $('.scroll-top').click(function(){
        $('html, body').animate({scrollTop : 0},1000);
    });

});
