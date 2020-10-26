var slectedHomeCatId = 0;
jQuery(document).ready(function ($) {

	$('.carousel-2').owlCarousel({
		loop: true,
		margin: 30,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 3,
				margin: 10,
			},
			600: {
				items: 5,
				margin: 10,
			},
			1000: {
				items: 6,
				margin: 10,
			},
			1200: {
				items: 7,
				margin: 20,
			}
		}
	})
	$('.carousel-10').owlCarousel({
		loop: true,
		margin: 20,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 3,
				margin: 10,
			},
			600: {
				items: 5,
				margin: 10,
			},
			1000: {
				items: 8,
				margin: 10,
			},
			1200: {
				items: 10,
				margin: 20,
			}
		}
	})
	$('.carousel-3').owlCarousel({
		loop: true,
		margin: 30,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
			},
			600: {
				items: 2,
			},
			1000: {
				items: 3,
			},
			1200: {
				items: 3,
			}
		}
	})
	$('.carousel-5').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 2,
				margin: 5,
				nav: true,
			},
			600: {
				items: 2,
				margin: 5,
			},
			1000: {
				items: 3,
				margin: 5,
			},
			1500: {
				items: 5,
				margin: 5,
			}
		}
	})
	$('.owl-slider-pd-ch').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
				margin: 0,
				nav: true,
			},
			600: {
				items: 1,
				margin: 0,
			},
			1000: {
				items: 1,
				margin: 0,
			},
			1200: {
				items: 1,
				margin: 0,
			}
		}
	})
	$('.owl-slider-pd-ch-list').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
				margin: 10,
				nav: true,
			},
			600: {
				items: 3,
				margin: 10,
			},
			1000: {
				items: 4,
				margin: 10,
			},
			1200: {
				items: 4,
				margin: 10,
			}
		}
	})
	$('.owl-slider-top-sud').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
				margin: 5,
				nav: true,
			},
			600: {
				items: 1,
				margin: 5,
			},
			1000: {
				items: 2,
				margin: 5,
			},
			1200: {
				items: 2,
				margin: 5,
			}
		}
	})
	$('.owl-slider-single').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: false,
		responsiveClass: true,
		dots: false,
		nav: false,
		responsive: {
			0: {
				items: 1,
				margin: 5,
				nav: true,
			},
			600: {
				items: 1,
				margin: 5,
			},
			1000: {
				items: 1,
				margin: 5,
			},
			1200: {
				items: 1,
				margin: 5,
			}
		}
	})
	$('.owl-slider-blog-sud').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
				margin: 5,
				nav: true,
			},
			600: {
				items: 2,
				margin: 5,
			},
			1000: {
				items: 4,
				margin: 5,
			},
			1200: {
				items: 4,
				margin: 5,
			}
		}
	})
	$('.owl-slider-event-details-sud').owlCarousel({
		loop: true,
		autoplay: 1000,
		autoplayHoverPause: true,
		responsiveClass: true,
		dots: false,
		nav: true,
		responsive: {
			0: {
				items: 1,
				margin: 10,
				nav: true,
			},
			600: {
				items: 2,
				margin: 10,
			},
			1000: {
				items: 3,
				margin: 10,
			},
			1200: {
				items: 3,
				margin: 10,
			}
		}
	})
	$('.carousel-7').owlCarousel({
		loop: true,
		// autoplay: 1000,
		// autoplayHoverPause: true,
		margin: 30,
		responsiveClass: true,
		dots: false,
		nav: true,
		startPosition: typeof startPositionOwlCarSeven === "undefined" ? 0 : startPositionOwlCarSeven,
		responsive: {
			0: {
				items: 3,
			},
			600: {
				items: 6,
			},
			1000: {
				items: 9,
			}
		}
	})

	$('#horizontalTab').easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion           
		width: 'auto', //auto or any width like 600px
		fit: true,   // 100% fit in a container
		/*closed: 'accordion',*/ // Start closed if in accordion view
		activate: function (event) { // Callback function if tab is switched
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

	$('.searchbtn').click(function () {
		$('.topsearch').addClass('scrchboxopen')
		$(this).hide();
	});
	$('.scrchclsbtn').click(function () {
		$('.topsearch').removeClass('scrchboxopen');
		$('.searchbtn').show();
	});
	$('.navbtn').click(function () {
		$('.headerright').toggleClass('menuopen')
		$(this).toggleClass('navtogl')
	});
	$('.menuclsbtn').click(function () {
		$('.menusection').removeClass('menuopen');
		$('.navbtn').show();
	});
	// $(".range-example-input-2").asRange({
	// 	range: true,
	// 	limit: false
	// });

	// // Product Gallery
	// $('#view').setZoomPicture({
	// 	thumbsContainer: '#pics-thumbs',
	// 	prevContainer: '#nav-left-thumbs',
	// 	nextContainer: '#nav-right-thumbs',
	// 	zoomContainer: '#zoom',
	// 	zoomLevel: 2,
	// });
	$('.logbtn').click(function () {
		$('.user-drop').slideToggle();
	});

	$(".toggle-password").click(function () {
		$(this).toggleClass("fa-eye fa-eye-slash");
		var input = $($(this).attr("toggle"));
		if (input.attr("type") == "password") {
			input.attr("type", "text");
		} else {
			input.attr("type", "password");
		}
	});
	$(".homeDd").click(function () {
		slectedHomeCatId = $(this).attr('data');
		$('#form_cat_id').val(slectedHomeCatId);
	});

	// Set
	var main = $('div.mm-dropdown .textfirst')
	var li = $('div.mm-dropdown > ul > li.input-option')
	var inputoption = $("div.mm-dropdown .option")
	var default_text = 'Select One';

	// Animation
	main.click(function () {
		main.html(default_text);
		li.toggle('fast');
	});

	// Insert Data
	li.click(function () {
		// hide
		li.toggle('fast');
		var livalue = $(this).data('value');
		var lihtml = $(this).html();
		main.html(lihtml);
		inputoption.val(livalue);
	});

});

$(window).scroll(function () {
	var sticky = $('.inner-page-wrap #header'),
		scroll = $(window).scrollTop();

	if (scroll >= 40) {
		sticky.addClass('innerchange');
	}
	else {
		sticky.removeClass('innerchange');
	}
});

$(window).bind("load resize", function () {
	var width = $(window).width();
	if (width <= 767) {

		$('.btn.pub-list').detach().insertAfter('#header .navbtn');
		$('.logbtn').detach().insertAfter('#header .navbtn');
		$('.user-drop').detach().insertAfter('.logbtn.after-log');
	}
	else {
		$('.btn.pub-list').detach().appendTo('#header .headerright > div:nth-child(2)');
		$('.logbtn').detach().appendTo('#header .headerright > div:nth-child(3)');
		$('.user-drop').detach().prependTo('.logout');
	}
});

function submitForm() {
	var form = $('#searchFormId');
	var cat_id = $('#catSelectedId').val();
	if (cat_id != '' && cat_id > 0) {
		cat_id = '/' + cat_id;
		form.attr('action', form.attr('action') + cat_id).trigger('submit');
	}
	return false;
}

function postsCarousel() {
	var checkWidth = $(window).width();
	var owlPost = $("#latest-posts .posts-wrapper");
	if (checkWidth > 767) {
		if (typeof owlPost.data('owl.carousel') != 'undefined') {
			owlPost.data('owl.carousel').destroy();
		}
		owlPost.removeClass('owl-carousel');
	} else if (checkWidth < 768) {
		owlPost.addClass('owl-carousel');
		owlPost.owlCarousel({
			items: 1,
			slideSpeed: 500,
			animateOut: 'fadeOut',
			touchDrag: false,
			mouseDrag: false,
			autoplay: true,
			autoplaySpeed: 8000,
			autoplayTimeout: 8000,
			dots: true,
			loop: true
		});
	}
}

postsCarousel();
$(window).resize(postsCarousel);