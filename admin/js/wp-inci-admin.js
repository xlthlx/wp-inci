function copyShort(copyText) {
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
}

function copyToClipboard(text) {
    if ( window.clipboardData && window.clipboardData.setData ) {
        return window.clipboardData.setData("Text", text);

    } else {
        const textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.getElementById('msg').style.display = 'inline-block';
            document.getElementById('msg').innerText = wi_msg;
            return document.execCommand("copy");
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

(function ($) {
    $(document).ready(function () {
        $("#safety").on("change", function () {
            let new_safety = $(this).val();

            switch (new_safety) {
                case "1":
                    new_safety = "gg";
                    break;
                case "2":
                    new_safety = "gw";
                    break;
                case "3":
                    new_safety = "yw";
                    break;
                case "4":
                    new_safety = "rw";
                    break;
                case "5":
                    new_safety = "rr";
                    break;
            }

            const new_first = new_safety.substring(0, 1);
            const new_second = new_safety.substring(1, 2);

            const first = $("div.first");
            const second = $("div.second");

            first.removeClass().addClass(new_first + " first").html(new_first.toUpperCase());
            second.removeClass().addClass(new_second + " second").html(new_second.toUpperCase());
        });

        $("#copy_style").on("click", function () {
            copyToClipboard(wi_style);
            setTimeout(function () {
                $('#msg').fadeOut('fast');
            }, 3000);
        });

        $('.cmb2-switch').each(function () {
            const checkbox = $(this).find('input[type="checkbox"]');
            const inactiveValue = checkbox.data('inactive-value');
            const activeValue = checkbox.val();
            $(this).on('click', function () {
                if ( checkbox.prop('checked') ) {
                    checkbox.val(activeValue);
                } else {
                    checkbox.val(inactiveValue);
                    checkbox.prop('checked', false).removeAttr('checked');
                }
            });
        });
    });
})(jQuery);