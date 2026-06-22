$(function() {
	"use strict";

	// Initialize PerfectScrollbar conditionally if elements exist in DOM
	if ($(".app-container").length) {
		new PerfectScrollbar(".app-container");
	}
	if ($(".header-message-list").length) {
		new PerfectScrollbar(".header-message-list");
	}
	if ($(".header-notifications-list").length) {
		new PerfectScrollbar(".header-notifications-list");
	}

	$(".mobile-search-icon").on("click", function() {
		$(".search-bar").addClass("full-search-bar");
	});

	$(".search-close").on("click", function() {
		$(".search-bar").removeClass("full-search-bar");
	});

	$(".mobile-toggle-menu").on("click", function() {
		$(".wrapper").addClass("toggled");
	});

	$(".dark-mode").on("click", function() {
		if($(".dark-mode-icon i").attr("class") == 'bx bx-sun') {
			$(".dark-mode-icon i").attr("class", "bx bx-moon");
			$("html").attr("class", "light-theme");
		} else {
			$(".dark-mode-icon i").attr("class", "bx bx-sun");
			$("html").attr("class", "dark-theme");
		}
	});

	$(".toggle-icon").click(function() {
		if ($(".wrapper").hasClass("toggled")) {
			$(".wrapper").removeClass("toggled");
			$(".sidebar-wrapper").unbind("hover");
		} else {
			$(".wrapper").addClass("toggled");
			$(".sidebar-wrapper").hover(function() {
				$(".wrapper").addClass("sidebar-hovered");
			}, function() {
				$(".wrapper").removeClass("sidebar-hovered");
			});
		}
	});

	$(window).on("scroll", function() {
		if ($(this).scrollTop() > 300) {
			$(".back-to-top").fadeIn();
		} else {
			$(".back-to-top").fadeOut();
		}
	});

	$(".back-to-top").on("click", function() {
		$("html, body").animate({
			scrollTop: 0
		}, 600);
		return false;
	});

	// Initialize MetisMenu
	$("#menu").metisMenu();

	$(".chat-toggle-btn").on("click", function() {
		$(".chat-wrapper").toggleClass("chat-toggled");
	});

	$(".chat-toggle-btn-mobile").on("click", function() {
		$(".chat-wrapper").removeClass("chat-toggled");
	});

	$(".email-toggle-btn").on("click", function() {
		$(".email-wrapper").toggleClass("email-toggled");
	});

	$(".email-toggle-btn-mobile").on("click", function() {
		$(".email-wrapper").removeClass("email-toggled");
	});

	$(".compose-mail-btn").on("click", function() {
		$(".compose-mail-popup").show();
	});

	$(".compose-mail-close").on("click", function() {
		$(".compose-mail-popup").hide();
	});

	$(".switcher-btn").on("click", function() {
		$(".switcher-wrapper").toggleClass("switcher-toggled");
	});

	$(".close-switcher").on("click", function() {
		$(".switcher-wrapper").removeClass("switcher-toggled");
	});

	$("#lightmode").on("click", function() {
		$("html").attr("class", "light-theme");
	});

	$("#darkmode").on("click", function() {
		$("html").attr("class", "dark-theme");
	});

	$("#semidark").on("click", function() {
		$("html").attr("class", "semi-dark");
	});

	$("#minimaltheme").on("click", function() {
		$("html").attr("class", "minimal-theme");
	});

	$("#headercolor1").on("click", function() {
		$("html").addClass("color-header headercolor1").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor2").on("click", function() {
		$("html").addClass("color-header headercolor2").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor3").on("click", function() {
		$("html").addClass("color-header headercolor3").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor4").on("click", function() {
		$("html").addClass("color-header headercolor4").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor5").on("click", function() {
		$("html").addClass("color-header headercolor5").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor6").on("click", function() {
		$("html").addClass("color-header headercolor6").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8");
	});

	$("#headercolor7").on("click", function() {
		$("html").addClass("color-header headercolor7").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});

	$("#headercolor8").on("click", function() {
		$("html").addClass("color-header headercolor8").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3");
	});

	// Sidebar colors 
	$('#sidebarcolor1').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor1'); });
	$('#sidebarcolor2').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor2'); });
	$('#sidebarcolor3').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor3'); });
	$('#sidebarcolor4').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor4'); });
	$('#sidebarcolor5').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor5'); });
	$('#sidebarcolor6').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor6'); });
	$('#sidebarcolor7').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor7'); });
	$('#sidebarcolor8').click(function() { $('html').attr('class', 'color-sidebar sidebarcolor8'); });
});

// Robust active highlighting matching sub-routes
$(function() {
	var currentPath = window.location.pathname.replace(/\/$/, "");
	$(".metismenu li a").each(function() {
		try {
			var linkUrl = new URL(this.href);
			if (linkUrl.origin !== window.location.origin) return;
			
			var linkPath = linkUrl.pathname.replace(/\/$/, "");
			var isBaseAdmin = linkPath === "" || linkPath === "/" || linkPath.endsWith("/admin");
			
			var isMatch = false;
			if (currentPath === linkPath) {
				isMatch = true;
			} else if (!isBaseAdmin) {
				if (currentPath.indexOf(linkPath + '/') === 0) {
					isMatch = true;
				}
			}
			
			if (isMatch) {
				$(this).addClass("active");
				var parentLi = $(this).closest("li").addClass("mm-active");
				var parents = parentLi.parents("ul");
				parents.each(function() {
					if ($(this).hasClass("metismenu")) return false;
					$(this).addClass("mm-show");
					$(this).closest("li").addClass("mm-active");
				});
			}
		} catch (err) {
			// Ignore invalid URLs
		}
	});
});