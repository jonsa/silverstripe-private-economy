(function ($) {
    $(document).ready(function () {
        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        function moveTooltip(x, y) {
            $("#tooltip").css({
                top: y + -20,
                left: x + 10
            });
        }

        var previousPoint = null;
        $(".chart").bind("plothover", function (event, pos, item) {
            if (item) {
                var point = item.dataIndex + "" + item.seriesIndex;
                if (previousPoint == point) {
                    moveTooltip(pos.pageX, pos.pageY);
                } else {
                    previousPoint = point;

                    $("#tooltip").remove();
                    var value = item.datapoint[1].toFixed(2);

                    showTooltip(pos.pageX, pos.pageY, item.series.label + ": " + value);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
    });
}(jQuery));