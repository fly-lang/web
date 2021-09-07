const config = {
  release: {
    version: {
      number: '0.0.4-alpha',
      prefix: 'v'
    },
    url: {
      download: 'https://github.com/fly-lang/fly/releases/download',
      releases: 'https://github.com/fly-lang/fly/releases'
    },
    prefix: 'fly-',
    suffix: {
      win: '-win-x64.zip',
      mac: '-macos-x86_64.tar.gz',
      linux: '-linux-x86_64.tar.gz'
    },
  },
  wiki: {
    url: 'https://github.com/fly-lang/fly/wiki',
    install: 'https://github.com/fly-lang/fly/wiki/Installing-Fly',
    languageReference: 'https://github.com/fly-lang/fly/wiki/Fly-Language-Reference',
    commandGuide: 'https://github.com/fly-lang/fly/wiki/Fly-Command-Guide',
    programmersManual: 'https://github.com/fly-lang/fly/wiki/Fly-Programmers-Manual'
  }
};

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

function getDownloadUrl() {
  return config.release.url.download + '/' + config.release.version.prefix + config.release.version.number;
}

function getDownloadWinUrl() {
  return getDownloadUrl() + '/' + config.release.prefix + config.release.version.number + config.release.suffix.win;
}

function getDownloadMacUrl() {
  return getDownloadUrl() + '/' + config.release.prefix + config.release.version.number + config.release.suffix.mac;
}

function getDownloadLinuxUrl() {
  return getDownloadUrl() + '/' + config.release.prefix + config.release.version.number + config.release.suffix.linux;
}

function download() {

  let fileName;
  let dataType;

  if (String(platform.os.toString()).indexOf("Win") != -1) {
    fileName = config.release.prefix + config.release.version.number + config.release.suffix.win;
    dataType = 'application/zip';
  } else if (String(platform.os.toString()).indexOf("Mac") != -1) {
    fileName = config.release.prefix + config.release.version.number + config.release.suffix.mac;
    dataType = 'application/gzip';
  } else if (String(platform.os.toString()).indexOf("Linux") != -1) {
    fileName = config.release.prefix + config.release.version.number + config.release.suffix.linux;
    dataType = 'application/gzip';
  } else {
    window.location.href = getDownloadUrl();
    return
  }

  let a = document.createElement("a");
  a.href = getDownloadUrl();
  a.setAttribute("href", 'data:' + dataType + ',' + encodeURIComponent(a.href))
  a.setAttribute("download", fileName);
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

