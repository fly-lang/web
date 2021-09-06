(function ($) {
  "use strict";

  // Header scroll class
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('#header').addClass('header-scrolled');
    } else {
      $('#header').removeClass('header-scrolled');
    }
  });

  if ($(window).scrollTop() > 100) {
    $('#header').addClass('header-scrolled');
  }

  // Smooth scroll for the navigation and links with .scrollto classes
  $('.main-nav a, .mobile-nav a, .scrollto').on('click', function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      if (target.length) {
        var top_space = 0;

        if ($('#header').length) {
          top_space = $('#header').outerHeight();

          if (! $('#header').hasClass('header-scrolled')) {
            top_space = top_space - 40;
          }
        }

        $('html, body').animate({
          scrollTop: target.offset().top - top_space
        }, 1500, 'easeInOutExpo');

        if ($(this).parents('.main-nav, .mobile-nav').length) {
          $('.main-nav .active, .mobile-nav .active').removeClass('active');
          $(this).closest('li').addClass('active');
        }

        if ($('body').hasClass('mobile-nav-active')) {
          $('body').removeClass('mobile-nav-active');
          $('.mobile-nav-toggle i').toggleClass('fa-times fa-bars');
          $('.mobile-nav-overly').fadeOut();
        }
        return false;
      }
    }
  });

  // Navigation active state on scroll
  var nav_sections = $('section');
  var main_nav = $('.main-nav, .mobile-nav');
  var main_nav_height = $('#header').outerHeight();

  $(window).on('scroll', function () {
    var cur_pos = $(this).scrollTop();
  
    nav_sections.each(function() {
      var top = $(this).offset().top - main_nav_height,
          bottom = top + $(this).outerHeight();
  
      if (cur_pos >= top && cur_pos <= bottom) {
        main_nav.find('li').removeClass('active');
        main_nav.find('a[href="#'+$(this).attr('id')+'"]').parent('li').addClass('active');
      }
    });
  });

})(jQuery);

function download(version) {
  var url = 'https://github.com/fly-lang/fly/releases/tag/v' + version + '/'
  if (String(platform.os.toString()).indexOf("Win") != -1) { fileName = 'fly-' + version + '-win-x64.zip' }
  else if (String(platform.os.toString()).indexOf("Mac") != -1) { fileName = 'fly-' + version + '-macos-x86_64.tar.gz' }
  else if (String(platform.os.toString()).indexOf("Linux") != -1) { fileName = 'fly-' + version + '-linux-x86_64.tar.gz' }
  else { window.location.href = url; return }

  var a = document.createElement("a");
  a.href = url;
  a.setAttribute("href", 'data:application/gzip,' + encodeURIComponent(url))
  a.setAttribute("download", fileName);
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

