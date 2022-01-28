(function ($) {
    $(function () {
        $('.cmb2-search-ajax').each(
            function () {
                const fid = $(this).attr('id');
                const query_args = $(this).attr('data-queryargs');
                const object = $(this).attr('data-object');
                $(this).devbridgeAutocomplete({
                    serviceUrl: wi.ajaxurl,
                    type: 'POST',
                    triggerSelectOnValidInput: true,
                    showNoSuggestionNotice: true,
                    noSuggestionNotice: wi.notice,
                    transformResult: function (r) {
                        const suggestions = $.parseJSON(r);
                        if ( $('#' + fid + '_results li').length ) {
                            const selected_vals = Array();
                            let d = 0;
                            $('#' + fid + '_results input').each(function () {
                                selected_vals.push($(this).val());
                            });
                            $(suggestions).each(function (ri, re) {
                                if ( $.inArray((re.data).toString(), selected_vals) > -1 ) {
                                    suggestions.splice(ri - d, 1);
                                    d++;
                                }
                            });
                        }
                        $(suggestions).each(function (ri, re) {
                            re.value = $('<textarea />').html(re.value).text();
                        });
                        return {suggestions: suggestions};
                    },
                    params: {
                        action: 'cmb2_search_ajax_get_results',
                        wicheck: wi.nonce,
                        object: object,
                        query_args: query_args,
                    },
                    onSearchStart: function () {
                        $(this).next('img.cmb2-search-ajax-spinner').css('display', 'inline-block');
                    },
                    onSearchComplete: function () {
                        $(this).next('img.cmb2-search-ajax-spinner').hide();
                    },
                    onSelect: function (suggestion) {
                        $(this).devbridgeAutocomplete('clearCache');
                        const lid = $(this).attr('id') + '_results';
                        const limit = $(this).attr('data-limit');
                        const sortable = $(this).attr('data-sortable');
                        if ( 1 !== limit ) {
                            const handle = (sortable === 1) ? '<span class="handle"></span>' : '';
                            $('#' + lid).append('<li>' + handle + '<input type="hidden" name="' + lid + '[]" value="' + suggestion.data + '"><a href="' + suggestion.guid + '" target="_blank" class="edit-link"><div class="wi_wrapper">' + suggestion.safety + '<div class="wi_value">' + suggestion.value + '</div></div></a><a class="remover"><span class="dashicons dashicons-no"></span><span class="dashicons dashicons-dismiss"></span></a></li>');
                            $(this).val('');
                            if ( limit === $('#' + lid + ' li').length ) {
                                $(this).prop('disabled', 'disabled');
                            } else {
                                $(this).focus();
                            }
                        } else {
                            $('input[name=' + lid + ']').val(suggestion.data);
                        }
                    }
                });

                if ( $(this).attr('data-sortable') === '1' ) {
                    $('#' + fid + '_results').sortable({
                        handle: '.handle',
                        placeholder: 'ui-state-highlight',
                        forcePlaceholderSize: true
                    });
                }

                if ( $(this).attr('data-limit') === '1' ) {
                    $(this).on('blur', function () {
                        if ( $(this).val() === '' ) {
                            const lid = $(this).attr('id') + '_results';
                            $('input[name=' + lid + ']').val('');
                        }
                    });
                }
            }
        );

        $('.cmb2-search-ajax-results').on('click', 'a.remover', function () {
            $(this).parent('li').fadeOut(400, function () {
                const iid = $(this).parents('ul').attr('id');
                $(this).remove();
                const remove = '#' + iid
                $(remove).removeProp('disabled');
                $(remove).devbridgeAutocomplete('clearCache');
            });
        });

    });
})(jQuery);
