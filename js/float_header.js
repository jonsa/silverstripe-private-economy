(function ($) {
var copied = false;
	
function UpdateTableHeaders() {
    $("div.divTableWithFloatingHeader").each(function() {
        var originalHeaderRow = $(".tableFloatingHeaderOriginal", this);
        var floatingHeaderRow = $(".tableFloatingHeader", this);
        var offset = $(this).offset();
        var scrollTop = $(window).scrollTop();
        if ((scrollTop > offset.top) && (scrollTop < offset.top + $(this).height())) {
            floatingHeaderRow.css("visibility", "visible");
            //floatingHeaderRow.css("top", Math.min(scrollTop - offset.top, $(this).height() - floatingHeaderRow.height()) + "px");

            if (!copied) {
	            // Copy cell widths from original header
	            $("th", floatingHeaderRow).each(function(index) {
	            	if (index == 0)
	            		floatingHeaderRow.css("left", $(originalHeaderRow).parent().offset().left + "px");
	                var cellWidth = $("th", originalHeaderRow).eq(index).css('width');
	                $(this).css('width', cellWidth);
	            });
	
	            // Copy row width from whole table
	            floatingHeaderRow.css("width", $(this).css("width"));
	            copied = true;
            }
        }
        else {
            floatingHeaderRow.css("visibility", "hidden");
            floatingHeaderRow.css("top", "0px");
            copied = false;
        }
    });
}

$(document).ready(function() {
    $(".TableField table, table.overview").each(function() {
        $(this).wrap("<div class=\"divTableWithFloatingHeader\" style=\"position:relative\"></div>");

        var originalHeaderRow = $("tr:first", this)
        originalHeaderRow.before(originalHeaderRow.clone());
        var clonedHeaderRow = $("tr:first", this)

        clonedHeaderRow.addClass("tableFloatingHeader");
        clonedHeaderRow.css("position", "fixed");
        clonedHeaderRow.css("top", "0px");
        clonedHeaderRow.css("left", $(this).css("margin-left"));
        clonedHeaderRow.css("visibility", "hidden");

        originalHeaderRow.addClass("tableFloatingHeaderOriginal");
    });
    UpdateTableHeaders();
    $(window).scroll(UpdateTableHeaders);
    $(window).resize(UpdateTableHeaders);
});

}(jQuery));