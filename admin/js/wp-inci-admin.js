(function ($) {
	$(document).ready(function () {
		$("#safety").on("change", function () {
			var new_safety = $(this).val();
			if (new_safety === "") {
				new_safety = "ww";
			}
			var new_first = new_safety.substring(0, 1);
			var new_second = new_safety.substring(1, 2);
			if (new_second === "") {
				new_second = "w";
			}
			var first = $("div.first");
			var second = $("div.second");

			first.removeClass().addClass(new_first + " first").html(new_first.toUpperCase());
			second.removeClass().addClass(new_second + " second").html(new_second.toUpperCase());
		});
	});
})(jQuery);
