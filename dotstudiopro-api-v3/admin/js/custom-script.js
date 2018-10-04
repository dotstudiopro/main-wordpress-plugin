/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

vex.defaultOptions.className = 'vex-theme-plain';
(function ($) {
    $('.reset-dsp-token').on('click', function (e) {
        e.preventDefault();
        loading.show('Please wait...');
        var url = $(this).data('target');
        var action = $(this).data('action');
        var nonce = $(this).data('nonce');
        $.post(
                url,
                {
                    'action': action,
                    'nonce': nonce,
                    'api_secret': $('#dotstudiopro_api_key').val()
                },
                function (response) {
                    vex.closeAll();
                    vex.dialog.open({
                        unsafeMessage: [
                            '<h3>Regenerate Token</h3>',
                            '<dl>',
                            response,
                            '<dl>'
                        ].join(''),
                        buttons: [
                            $.extend({}, vex.dialog.buttons.YES, {
                                text: 'OK'
                            })
                        ],
                        focusFirstInput: false,
                        showCloseButton: true,
                        afterOpen: function () {
                            $('body').css('overflow', 'hidden');
                        },
                        afterClose: function () {
                            $('body').css('overflow', '');
                        }
                    });
                }
        );
    });
    var loading = window.loading = function () {

        var show = function (message) {

            var msg = (message != null) ? message : 'Loading';

            $dialog = vex.dialog.open({
                unsafeMessage: [
                    '<div style="margin: 0 auto; background:url(http://192.168.0.20/ds-pro/wp-content/plugins/dotstudiopro-api-v3/admin/images/hourglass.gif) no-repeat center center;width:150px;height:150px;"></div>',
                    '<h3 style="text-align:center;">',
                    msg,
                    '</h3>'
                ].join(''),
                buttons: false,
                focusFirstInput: false,
                showCloseButton: false,
                escapeButtonCloses: false,
                overlayClosesOnClick: false,
                afterOpen: function () {
                    $('body').css('overflow', 'hidden');
                },
                afterClose: function () {
                    $('body').css('overflow', '');
                }
            });
        };

        return {
            show: show
        };
    }();



    // Add Color Picker to all inputs that have 'color-field' class
    $(function () {
        $('.color-field').wpColorPicker();
    });

    // Toggle eye open-close
    $(".toggle-password-field").click(function () {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

})(jQuery);