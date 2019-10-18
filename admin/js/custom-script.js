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
var limit = customVars.limit; 
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

    $('#submit-api-data').on('click', function (e) {
        e.preventDefault();
        var data = $('#dsp_api_form').serializeArray();
        dataObj = {};
        $(data).each(function (i, field) {
            dataObj[field.name] = field.value;
        });
        var action = dataObj['action'];
        var nonce = dataObj['_dsp_nonce'];
        var btn_value = $(this).attr('id');

        loading.show('Step 1: Activate API key in-progress.');
        $('.dsp-box-hidden').show();
        $('.ajax-resp').append('<div><img class="activation-img" src="' + loader_gif + '"><p> Activate API key in-progress.</p></div>');
        AjaxcallForAPIkey(action, dataObj['dotstudiopro_api_key'], nonce, btn_value);

    });

    /**
     * Ajax event to Deactivate DotstudioPro Api key
     * After Conformation delete the Category and Channels for DSP Dashboard and Deactivate the API key.
     *
     * @since 1.0.0
     */

    $('#deactivate-api-data').on('click', function (e) {
        e.preventDefault();
        var data = $('#dsp_api_form').serializeArray();
        dataObj = {};
        $(data).each(function (i, field) {
            dataObj[field.name] = field.value;
        });
        var action = dataObj['action'];
        var nonce = dataObj['_dsp_nonce'];
        var btn_value = $(this).attr('id');

        vex.closeAll();
        vex.dialog.open({
            unsafeMessage: [
                '<d1>All Categories and Channels data will be wiped out by deactivating the API key.<d1>',
                '<dl>Are you still want to deactivate the API key?<dl>'
            ].join(''),
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, {
                    text: 'YES',
                    click: function () {
                        $('.dsp-box-hidden').show();
                        $('.ajax-resp').append('<div><img class="activation-img" src="' + loader_gif + '"><p> Deactivating API key.</p></div>');
                        loading.show('Deactivating API key.');
                        AjaxcallForAPIkey(action, dataObj['dotstudiopro_api_key'], nonce, btn_value);
                    }
                }),
                $.extend({}, vex.dialog.buttons.NO, {
                    text: 'NO',
                    click: function () {
                        vex.closeAll()
                    }
                })
            ],
            afterOpen: function () {
                $('body').css('overflow', 'hidden');
            },
        })
    });

    /**
     * Common Ajax Function to activating and deactivating the Key
     *
     * @since 1.0.0
     *
     * @param {type} action
     * @param {type} dotstudiopro_api_key
     * @param {type} nonce
     * @param {type} btn_value
     */

    function AjaxcallForAPIkey(action, dotstudiopro_api_key, nonce, btn_value) {

        var step1 = $.post(
                url,
                {
                    'action': action,
                    'dotstudiopro_api_key': dotstudiopro_api_key,
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
                            //loading.show('Step 3: Import channels in-progress.');
                            var step3 = importChannelData(url, 'import_channel_post_data', dataObj['_channel_nonce'], 1);
                            return step3;
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
            dialogresponse('Api key activation', (response.responseJSON) ? response.responseJSON.data.message : response.statusText)
            console.log("error in step 1");
        });

    }

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
        if(action == 'import_channel_post_data'){
            var data = importChannelData(url, action, nonce, 1);
            return data;
        }
        else{
        var data = importData(url, action, nonce)
        data.done(function (response) {
            if (response.success)
                dialogresponse('Data successfully imported ', response.data.message)
        });
        }
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
        var random_string = Math.random().toString(36).substring(7);
        var hash_key = new Date().getTime()+'_'+random_string;
        var step2 = $.post(
                url,
                {
                    'action': action,
                    'nonce': nonce,
                    'hash_key': hash_key,
                    async: false
                });
        step2.fail(function (response) {
            $('.ajax-resp').append('<div><img class="import-cat-img" src="' + error_img + '"><p> Error in Import process.</p></div>');
            dialogresponse('Error in Import process', (response.responseJSON) ? response.responseJSON.data.message : response.statusText)
            console.log("error in import data");
        })
        return step2;
    }

    function importChannelData(url, action, nonce, page, hash_key = '') {
        var start = (page - 1) * limit;
        var end = page * limit;
        var str = 'Importing ' + (start+1) + ' to ' + end + ' channels';
        var random_string = Math.random().toString(36).substring(7);
        var hash_key = (hash_key) ? hash_key : new Date().getTime()+'_'+random_string;
        vex.closeTop();
        loading.show('Step 3: Import channels in-progress.<br/>'+str);
        var step2 = $.post(
                url,
                {
                    'action': action,
                    'nonce': nonce,
                    'page': page,
                    'hash_key': hash_key,
                    async: false
                });
        step2.fail(function (response) {
            $('.ajax-resp').append('<div><img class="import-cat-img" src="' + error_img + '"><p> Error in Import process.</p></div>');
            dialogresponse('Error in Import process', (response.responseJSON) ? response.responseJSON.data.message : response.statusText)
            console.log("error in import data");
        })
        step2.done(function (response) {
            console.log(response);
            if (response.data.status == 'complete'){
                 $('.import-chnl-img').attr('src', success_img);
                dialogresponse('Data successfully imported ', response.data.message);
            }
            else
                importChannelData(url, action, nonce, response.data.page + 1, response.data.hash_key);
        })
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
            if (querystring == 'post_type=channel-category' || querystring == 'post_type=channel')
                $('.page-title-action').remove();
        }
    });

})(jQuery);