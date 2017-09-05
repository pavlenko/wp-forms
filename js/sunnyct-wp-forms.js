// Form ajax handler
$(document).on('submit', 'form[data-role="sunnyct-wp-forms"]', function(event){
    event.preventDefault();

    var $form      = $(this);
    var $container = $form.parents('[data-role="sunnyct-wp-forms-container"]');
    var $spinner   = $form.siblings('[data-role="sunnyct-wp-forms-spinner"]');

    $.ajax( {
        type: 'POST',
        url: $form.attr('action'),
        data: $form.serializeArray(),
        /**
         * @param {jqXHR}  jqXHR
         * @param {object} settings
         */
        beforeSend: function(jqXHR, settings){
            $spinner.show();
        },
        /**
         * @param {string|object} data
         * @param {string}        textStatus
         * @param {jqXHR}         jqXHR
         */
        success: function(data, textStatus, jqXHR){
            var html = $(jqXHR.responseText);
            $container.replaceWith(html);
            $(document).trigger('sunnyct-wp-forms-ajax-success', [html.find('form').first()]);
        },
        /**
         * @param {jqXHR}  jqXHR
         * @param {string} textStatus
         * @param {string} errorThrown
         */
        error: function(jqXHR, textStatus, errorThrown){
            var html = $(jqXHR.responseText);
            $container.replaceWith(html);
            $(document).trigger('sunnyct-wp-forms-ajax-error', [html.find('form').first()]);
        },
        /**
         * @param {jqXHR}  jqXHR
         * @param {string} textStatus
         */
        complete: function(jqXHR, textStatus){
            $spinner.hide();
        },
    });
});

// Save original modal body for restore after hide
$(document).on('show.bs.modal', function(event){
    var $target    = $(event.target);
    var $container = $target.find('[data-role="sunnyct-wp-forms-container"]');

    if ($container.length) {
        $target.data('sunnyct-wp-form-parent', $container.parent());
        $target.data('sunnyct-wp-form-html', $container.parent().html());
    }
});

// Restore original modal body
$(document).on('hide.bs.modal', function(event){
    var $target = $(event.target);

    if ($target.data('sunnyct-wp-form-parent')) {
        $($target.data('sunnyct-wp-form-parent')).html($target.data('sunnyct-wp-form-html'));
    }
});

function sunnyct_wp_form_recaptcha_update()
{
    if ('undefined' !== typeof grecaptcha) {
        $('.g-recaptcha').each(function () {
            var params = {
                'sitekey': $(this).data('sitekey'),
                'type':    $(this).data('type'),
                'size':    $(this).data('size'),
                'theme':   $(this).data('theme'),
            };

            var callback = $(this).data('callback');

            if (callback && 'function' === typeof window[callback]) {
                params['callback'] = window[callback];
            }

            var expired_callback = $(this).data('expired-callback');

            if (expired_callback && 'function' === typeof window[expired_callback]) {
                params['expired-callback'] = window[expired_callback];
            }

            $(this).attr('data-recaptcha-id', grecaptcha.render(this, params));
        });
    }
}