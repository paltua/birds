jQuery(document).ready(function ($) { 

	$('.carousel-4').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    responsive:{
	        0:{
	            items:1,
	            nav:true
	        },
	        600:{
	            items:3,
	            nav:false
	        },
	        1000:{
	            items:4,
	            nav:true,
	            loop:false
	        }
	    }
	})
	$('.carousel-7').owlCarousel({
	    loop:true,
	    margin:30,
	    responsiveClass:true,
	    dots: false,
	    responsive:{
	        0:{
	            items:1,
	            nav:true
	        },
	        600:{
	            items:3,
	            nav:false
	        },
	        1000:{
	            items:7,
	            nav:true,
	            loop:false
	        }
	    }
	})

	$('#horizontalTab').easyResponsiveTabs({
	type: 'default', //Types: default, vertical, accordion           
	width: 'auto', //auto or any width like 600px
	fit: true,   // 100% fit in a container
	closed: 'accordion', // Start closed if in accordion view
	activate: function(event) { // Callback function if tab is switched
	var $tab = $(this);
	var $info = $('#tabInfo');
	var $name = $('span', $info);
	$name.text($tab.text());
	$info.show();
	}		
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
	    $('.menusection').addClass('menuopen')
	    $(this).hide();
	});
	$('.menuclsbtn').click(function() {
	    $('.menusection').removeClass('menuopen');
	    $('.navbtn').show();
	});
    
});