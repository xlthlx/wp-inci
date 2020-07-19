(function ($) {
	$(document).ready(function () {
		$("#safety").on("change", function () {
			var new_safety = $(this).val();

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

			var new_first = new_safety.substring(0, 1);
			var new_second = new_safety.substring(1, 2);

			var first = $("div.first");
			var second = $("div.second");

			first.removeClass().addClass(new_first + " first").html(new_first.toUpperCase());
			second.removeClass().addClass(new_second + " second").html(new_second.toUpperCase());
		});
	});
})(jQuery);

function copyShort(copyText) {
	copyText.select();
	copyText.setSelectionRange(0, 99999)
	document.execCommand("copy");
}
