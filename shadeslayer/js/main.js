(function($) {
  
  "use strict";

    /* 
   wow js
   ========================================================================== */
    //Initiat WOW JS
    new WOW().init();

    $(window).on('load', function() {

        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 200) {
                $( ".nav-arc" ).fadeOut( "fast", function() {
				});
            } else {
				$( ".nav-arc" ).fadeIn( "fast", function() {
				});
            }
        });

    });

  /* 
   SMOOTH SCROLL
   ========================================================================== */
    var scrollAnimationTime = 1200,
        scrollAnimation = 'easeInOutExpo';

    $('a.scrollto').on('bind', 'click.smoothscroll', function (event) {
        event.preventDefault();
        var target = this.hash;
        
        $('html, body').stop().animate({
            'scrollTop': $(target).offset().top
        }, scrollAnimationTime, scrollAnimation, function () {
            window.location.hash = target;
        });
    });

/* 
   Back Top Link
   ========================================================================== */
    var offset = 200;
    var duration = 500;
    $(window).scroll(function() {
      if ($(this).scrollTop() > offset) {
        $('.back-to-top').fadeIn(400);
      } else {
        $('.back-to-top').fadeOut(400);
      }
    });

    $('.back-to-top').on('click',function(event) {
      event.preventDefault();
      $('html, body').animate({
        scrollTop: 0
      }, 600);
      return false;
    })
	
	$('.scroll-down').on('click',function(event) {
      event.preventDefault();
      $('html, body').animate({
        scrollTop: $("#game").offset().top
      }, 600);
      return false;
    })




/* 
   Page Loader
   ========================================================================== */
  $('#loader').fadeOut();

}(jQuery));

document.getElementById("body").onscroll = function myFunction() {  
    var scrolltotop = document.scrollingElement.scrollTop;
    var target = document.getElementById("hero-small");
    var xvalue = "center";
    var factor = 0.4;
    var yvalue = scrolltotop * factor;
    target.style.backgroundPosition = xvalue + " " + yvalue + "px";
  }