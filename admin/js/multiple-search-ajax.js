(function ($) {
    $(document).ready(function ($) {
        $('.cmb2-multiple-search-ajax').each(
            function () {
                const tid = $(this).attr('id');
                const btn = $(this).parent().children("button").attr('id');
                const obj = $('#' + tid);
                const lid = obj.prevAll('.cmb2-search-ajax').first().attr('id');
                $('#' + btn).on('click', function () {
                    const text = obj.val();
                    $(this).next('img.cmb2-multiple-search-ajax-spinner').css('display', 'inline-block');
                    obj.val('');
                    $.ajax({
                        url: wi_mu.ajaxurl,
                        type: 'post',
                        data: {
                            'text': text,
                            'field_id': lid,
                            'wimucheck': wi_mu.nonce,
                            'action': 'cmb2_multiple_search_ajax_get_results'
                        },
                        success: function (response) {
                            const data = JSON.parse(response);

                            $('#' + tid).val(data.string);
                            $('#' + btn).next('img.cmb2-multiple-search-ajax-spinner').hide();

                            $.each(data.row, function (key, value) {
                                $('#' + lid + '_results').append(value);
                            });
                        },
                    });
                });
            }
        );
    });
})(jQuery);