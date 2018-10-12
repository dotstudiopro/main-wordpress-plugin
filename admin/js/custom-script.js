/**
 * The file that defines the global Javscript for the plugin 
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/admin
 */

vex.defaultOptions.className = 'vex-theme-plain';

var success_img = customVars.basedir + '/admin/images/true.png';
var loader_gif = customVars.basedir + '/admin/images/Rolling.gif';
var error_img = customVars.basedir + '/admin/images/false.png';
var url = customVars.ajaxurl;
(function ($) {

    /**
     * Ajax event to reset token form DotstudioPro Api Dashboard
     * 
     * @since 1.0.0
     */

    $('.reset-dsp-token').on('click', function (e) {
        e.preventDefault();
        loading.show('Please wait...');
        var action = $(this).data('action');
        var nonce = $(this).data('nonce');
        var reset_token = $.post(
                url,
                {
                    'action': action,
                    'api_secret': $('#dotstudiopro_api_key').val(),
                    'nonce': nonce
                }
        );

        reset_token.done(function (response) {
            dialogresponse('Regenerate Token', response.data.message)
        });

        reset_token.fail(function (response) {
            dialogresponse('Regenerate Token', response.responseJSON.data.message)
        })

    });



    /**
     * Ajax event to validate DotstudioPro Api key 
     * After Validation Import the Category and Channels for DSP Dashboard.
     * 
     * @since 1.0.0
     */

    $('#dsp_api_form').on('submit', function (e) {

        e.preventDefault();

        var data = jQuery(this).serializeArray();
        var btn_value = $("input[type=submit][clicked=true]").attr('id');

        dataObj = {};
        $(data).each(function (i, field) {
            dataObj[field.name] = field.value;
        });

        var action = dataObj['action'];
        var nonce = dataObj['_dsp_nonce'];

        if (btn_value != 'deactivate-api-data') {
            loading.show('Step 1: Activate API key in-progress.');
            $('.dsp-box-hidden').show();
            $('.ajax-resp').append('<div><img class="activation-img" src="' + loader_gif + '"><p> Activate API key in-progress.</p></div>');
        } else {
            $('.dsp-box-hidden').show();
            $('.ajax-resp').append('<div><img class="activation-img" src="' + loader_gif + '"><p> Deactivating API key.</p></div>');
            loading.show('Deactivating API key.');
        }

        var step1 = $.post(
                url,
                {
                    'action': action,
                    'dotstudiopro_api_key': dataObj['dotstudiopro_api_key'],
                    '_dsp_nonce': nonce,
                    'btn_value': btn_value,
                }
        );

        step1.done(function (response) {
            if (response.success) {
                if (btn_value == 'submit-api-data') {
                    $('.activation-img').attr('src', success_img);
                    $('.ajax-resp').append('<div><img class="import-cat-img" src=' + loader_gif + '><p> Import categories in-progress.</p></div>');
                    vex.closeTop();
                    loading.show('Step 2: Import categories in-progress.');
                    var step2 = importData(url, 'import_category_post_data', dataObj['_category_nonce'])
                    step2.done(function (response) {
                        if (response.success) {
                            $('.import-cat-img').attr('src', success_img);
                            $('.ajax-resp').append('<div><img class="import-chnl-img" src=' + loader_gif + '><p> Import channels in-progress.</p></div>');
                            vex.closeTop();
                            loading.show('Step 3: Import channels in-progress.');
                            var step3 = importData(url, 'import_channel_post_data', dataObj['_channel_nonce'])
                            step3.done(function (response) {
                                if (response.success)
                                    $('.import-chnl-img').attr('src', success_img);
                                dialogresponse('Api key activation', 'All the categories and channels are imported.')
                            });
                        }
                    });
                } else {
                    $('.activation-img').attr('src', success_img);
                    dialogresponse('Api key de-activation', 'Api key de-activation successfully.')
                }
            }
        });

        step1.fail(function (response) {
            $('.ajax-resp').html('<div><img class="import-cat-img" src="' + error_img + '"><p> Error: API key is not activated.</p></div>');
            dialogresponse('Api key activation', response.responseJSON.data.message)
            console.log("error in step 1");
        });

    });

    /**
     * Function to check which button is clicked
     * 
     * @since 1.0.0
     */
    $("form input[type=submit]").click(function () {
        $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    /**
     * Ajax event to Import Categories and Channels When Import Button is clicked.
     * 
     * @since 1.0.0
     */

    $(document).on('click', '.import_categories,.import_channels', function (e) {
        e.preventDefault();
        loading.show('Import process in-progress... Please wait...');
        var action = $(this).data('action');
        var nonce = $(this).data('nonce');
        var data = importData(url, action, nonce)
        data.done(function (response) {
            if (response.success)
                dialogresponse('Data successfully imported ', response.data.message)
        });
    });

    /**
     * Comman Ajax Call to get the Categories and Channels
     * 
     * @param {type} url
     * @param {type} action
     * @param {type} nonce
     * @param {type} callback
     * @since 1.0.0
     * 
     * @returns json
     */

    function importData(url, action, nonce) {
        var step2 = $.post(
                url,
                {
                    'action': action,
                    'nonce': nonce,
                    async: false
                });
        step2.fail(function (response) {
            $('.ajax-resp').append('<div><img class="import-cat-img" src="' + error_img + '"><p> Error in Import process.</p></div>');
            dialogresponse('Error in Import process', response.responseJSON.data.message)
        })
        return step2;
    }

    /**
     *
     * This function is used to display the final output of the responce in dialog (model popup)
     *  
     * @param {type} title
     * @param {type} message
     * @since 1.0.0
     */

    function dialogresponse(title, message) {
        vex.closeAll();
        vex.dialog.open({
            unsafeMessage: [
                '<h3>' + title + '</h3>',
                '<dl>' + message + '<dl>'
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
                window.location.reload();
            }
        });
        $('.vex-dialog-button-primary').focus();
    }

    /**
     * jQuery function to display loader during ajax event.
     * 
     * @type |window.loading|Window.loading
     * @since 1.0.0
     * 
     */
    var loading = window.loading = function () {
        var show = function (message) {
            var msg = (message != null) ? message : 'Loading';
            $dialog = vex.dialog.open({
                unsafeMessage: [
                    '<div style="margin: 0 auto; background:url(' + customVars.basedir + '/admin/images/hourglass.gif) no-repeat center center;width:150px;height:150px;"></div>',
                    '<h4 style="text-align:center;">',
                    msg,
                    '</h4>'
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
            $('.vex-dialog-button-primary').focus();
        };
        return {
            show: show
        };
    }();

    /**
     * Add Color Picker to all inputs that have 'color-field' class
     * 
     * @since 1.0.0
     */
    $(function () {
        $('.color-field').wpColorPicker();
    });

    /**
     * Toggle eye open-close for API Key Field.
     * 
     * @since 1.0.0
     */

    $(".toggle-password-field").click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    /**
     * Hide Add new Button
     * 
     * @since 1.0.0
     */

    $(function () {
        if ($('.page-title-action').length) {
            var querystring = $('.page-title-action').prop('href').split("?")[1];
            if (querystring == 'post_type=category' || querystring == 'post_type=channel')
                $('.page-title-action').remove();
        }
    });

})(jQuery);