(function($) {
    'use strict';

    // -------------------------------------------------------------------------------------------------
    // NAVBAR SEARCH ................ Navbar search
    // SIDEBAR NAV .................. Sidebar nav
    // -------------------------------------------------------------------------------------------------

    $(document).ready(function() {
        sidebarNav();
        flatpickr();
        tooltips();
        sidebarCollapse();
    });

    // -------------------------------------------------------------------------------------------------
    // SIDEBAR
    // -------------------------------------------------------------------------------------------------

    function sidebarNav() {
        $('.sidebar-nav__link').on('click', function(event) {
            var el = $(this);
            var isActive = el.parent().hasClass('is-active');

            $('.sidebar-nav__item').removeClass('is-active');

            if (!isActive) {
                el.parent().addClass('is-active');
            }

            if ($('body').hasClass('sidebar-sm') || $('body').hasClass('sidebar-md')) {
                var offsetTop = 0;

                if ($('body').hasClass('sidebar-sm')) {
                    offsetTop = $('.sidebar').position().top + el.offset().top - $('body').scrollTop() - el.closest('.sidebar-nav__item').height() - 1;
                } else if ($('body').hasClass('sidebar-md')) {
                    offsetTop = $('.sidebar').position().top + el.position().top + el.closest('.sidebar-nav__item').height();
                }

                $('.sidebar-subnav').not(el.next()).slideUp(0);
                el.next().slideToggle(0);
                el.next().css('top', offsetTop);
            } else {
                $('.sidebar-subnav').not(el.next()).slideUp(150);
                el.next().slideToggle(150);
            }

            setTimeout(function () {
                $(document).trigger('recalculate-sidebar-scroll');
            }, 200);

            if (el.closest('.sidebar-nav__item').find('.sidebar-subnav').length) {
                return false;
            }
        });
    }

    $('.sidebar-section-nav__link').on('click', function(event) {
        var el = $(this);
        var isActive = el.parent().hasClass('is-active');

        $('.sidebar-section-nav__item').removeClass('is-active');

        if (!isActive) {
            el.parent().addClass('is-active');
        }

        if ($('body').hasClass('sidebar-sm') || $('body').hasClass('sidebar-md')) {
            var offsetTop = 0;

            if ($('body').hasClass('sidebar-sm')) {
                offsetTop = $('.sidebar-section').position().top + el.offset().top - $('body').scrollTop() - el.closest('.sidebar-section-nav__item').height() - 1;
            } else if ($('body').hasClass('sidebar-md')) {
                offsetTop = $('.sidebar-section').position().top + el.position().top + el.closest('.sidebar-section-nav__item').height();
            }

            $('.sidebar-section-subnav').not(el.next()).slideUp(0);
            el.next().slideToggle(0);
            el.next().css('top', offsetTop);
        } else {
            $('.sidebar-section-subnav').not(el.next()).slideUp(150);
            el.next().slideToggle(150);
        }

        setTimeout(function () {
            $(document).trigger('recalculate-sidebar-scroll');
        }, 200);

        if (el.closest('.sidebar-section-nav__item').find('.sidebar-section-subnav').length) {
            return false;
        }
    });

    function flatpickr() {
        $('.flatpickr').flatpickr({
            altInput: true
        });
    }

    function tooltips() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    function toggleSidebar() {
        $('body').toggleClass('sidebar-sm');

        if ($('body').hasClass('sidebar-sm')) {
            initSidebarScrollbar(false);
        } else {
            initSidebarScrollbar(true);
        }

        setTimeout(function () {
            $(document).trigger('recalculate-sidebar-scroll');
        }, 200);
    }

    function sidebarCollapse() {
        $('.sidebar__collapse').on('click', function () {
            toggleSidebar();
        });
    }

    $(document).on('collapse-sidebar', function () {
        toggleSidebar();
    });

    $('.textavatar').each(function () {
        $(this).textAvatar({
            width: $(this).data('width'),
            height: $(this).data('height')
        });
    });

    $('.sidebar-toggler').on('click', function () {
        $('body').toggleClass('sidebar-is-opened');
    });

    $(document).on('click', function (e) {
        if ($('body').hasClass('sidebar-is-opened') && !$(e.target).closest('.sidebar-toggler').length) {
            if (!$(e.target).closest('.sidebar').length) {
                $('body').removeClass('sidebar-is-opened');
            }
        }

        if ($('.navbar-collapse').hasClass('show') && !$(e.target).closest('.navbar-toggler').length) {
            if (!$(e.target).closest('.navbar-collapse').length) {
                $('.navbar-collapse').removeClass('show');
                $('body').removeClass('is-navbar-opened');
            }
        }

        // Hide sidebar submenu if sidebar collapsed
        if ($('body').hasClass('sidebar-sm') || $('body').hasClass('sidebar-md')) {
            if (!$(e.target).closest('.sidebar').length) {
                $('.sidebar-subnav').hide();
                $('.sidebar-nav__item').removeClass('is-active');
            }
        }
    });

    $('.navbar-toggler').on('click', function () {
        $('body').toggleClass('is-navbar-opened');
    });

    if ($('.preloader').length) {
        setTimeout(function () {
            $('.preloader').fadeOut(0);
            $('body').removeClass('js-loading');
        }, 1500);
    }

    $('.color-checkbox :checkbox').on('change', function () {
        var parent = $(this).closest('.color-checkbox');

        if (this.checked) {
            parent.addClass('is-checked');
        } else {
            parent.removeClass('is-checked');
        }
    });

    $('.color-radio :radio').on('click', function () {
        var parent = $(this).closest('.color-radio');
        var name = $(this).attr('name');

        $('.color-radio input[name="' + name + '"]').each(function () {
            $(this).closest('.color-radio').removeClass('is-checked');
        });

        parent.addClass('is-checked');
    });

    $('.btn').on('mouseup', function () {
        var self = this;

        setTimeout(function () {
            self.blur();
        }, 500);
    });

    $('.js-datepicker').flatpickr();
    $('[data-toggle="popover"]').popover()
})(jQuery);
