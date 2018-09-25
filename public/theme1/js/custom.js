jQuery(document).ready(function ($) { 

	$('.carousel-2').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    nav:true,
	    responsive:{
	        0:{
	            items:1,
	        },
	        600:{
	            items:1,
	        },
	        1000:{
	            items:2,
	        },
	        1200:{
	            items:2,
	        }
	    }
	})
	$('.carousel-3').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    nav:true,
	    responsive:{
	        0:{
	            items:1,
	        },
	        600:{
	            items:2,
	        },
	        1000:{
	            items:3,
	        },
	        1200:{
	            items:3,
	        }
	    }
	})
	$('.carousel-5').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    nav:true,
	    responsive:{
	        0:{
	            items:2,
	        },
	        600:{
	            items:4,
	        },
	        1000:{
	            items:6,
	        },
	        1200:{
	            items:7,
	        }
	    }
	})
	$('.carousel-7').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    nav:true,
	    responsive:{
	        0:{
	            items:1,
	        },
	        600:{
	            items:3,
	        },
	        1000:{
	            items:7,
	        }
	    }
	})

	$('#horizontalTab').easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion           
		width: 'auto', //auto or any width like 600px
		fit: true,   // 100% fit in a container
		/*closed: 'accordion',*/ // Start closed if in accordion view
		activate: function(event) { // Callback function if tab is switched
			var $tab = $(this);
			var $info = $('#tabInfo');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}		
	});

	$('#verticalTab').easyResponsiveTabs({
	type: 'vertical',
	width: 'auto',
	fit: true
	});
	
	$('.searchbtn').click(function() {
	    $('.topsearch').addClass('scrchboxopen')
	    $(this).hide();
	});
	$('.scrchclsbtn').click(function() {
	    $('.topsearch').removeClass('scrchboxopen');
	    $('.searchbtn').show();
	});
	$('.navbtn').click(function() {
	    $('.headerright').toggleClass('menuopen')
	    $(this).toggleClass('navtogl')
	});
	$('.menuclsbtn').click(function() {
	    $('.menusection').removeClass('menuopen');
	    $('.navbtn').show();
	});
    $(".range-example-input-2").asRange({
	    range: true,
	    limit: false
	});

	// Product Gallery
    $('#view').setZoomPicture({
		thumbsContainer: '#pics-thumbs',
		prevContainer: '#nav-left-thumbs',
		nextContainer: '#nav-right-thumbs',
		zoomContainer: '#zoom',
		zoomLevel: 2,
    });
});

$(window).scroll(function() {
  var sticky = $('.inner-page-wrap #header'),
    scroll = $(window).scrollTop();
   
  if (scroll >= 150) { 
    sticky.addClass('innerchange'); 
	}
  else { 
   sticky.removeClass('innerchange');
	}
});