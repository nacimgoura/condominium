(function($) {
    'use strict';

    // -------------------------------------------------------------------------------------------------
    // NAVBAR SEARCH ................ Navbar search
    // SIDEBAR NAV .................. Sidebar nav
    // -------------------------------------------------------------------------------------------------

    $(document).ready(function() {
        sidebarNav();
        select2();
        flatpickr();
        tooltips();
        sidebarCollapse();
        editorHtml();
        datatable();
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


    function select2() {
        $('select').each(function () {
            var options = {
                placeholder: function() {
                    var el = $(this);
                    el.data('placeholder');
                }
            };

            if (!$(this).is('[data-search-enable]')) {
                options['minimumResultsForSearch'] = Infinity;
            }

            $(this).select2(options);
        });

        $('.select2').click(function(e) {
            var el = $(this);
            el.find('b').hide();
        });
    }

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

    function editorHtml() {
        try {
            tinymce.init({
                selector: '.editor-html',
                height: 200,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code help'
                ],
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_css: [
                    '//www.tinymce.com/css/codepen.min.css']
            });
        } catch (error) {

        }
    }

    function datatable() {
        if(!jQuery().DataTable) {
            return;
        }
        var table = $('#datatable').DataTable({
            lengthChange: false,
            buttons: [
                'print', 'excel', 'pdf', 'colvis'
            ],
            select: true,
            language: {
                processing:     "Traitement en cours...",
                search:         "Rechercher&nbsp;:",
                lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
                info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                infoPostFix:    "",
                loadingRecords: "Chargement en cours...",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "Aucune donnée disponible dans le tableau",
                paginate: {
                    first:      "Premier",
                    previous:   "Pr&eacute;c&eacute;dent",
                    next:       "Suivant",
                    last:       "Dernier"
                },
                aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
        table.buttons().container().appendTo('#datatable_wrapper .col-md-6:eq(0)');
    }

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
