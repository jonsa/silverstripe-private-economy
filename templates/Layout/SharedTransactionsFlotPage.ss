<div id="pageContent">
    <section class="grid_12">

        <h1>$Title</h1>

        $Content
        <% control TotalPointData %>
        <h2><% _t('TOTAL', 'Total') %></h2>
        <div class="chart" id="chart-total-point"></div>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var data = $Data;
                    $.plot("#chart-total-point", data, {
                        series: {
                            lines: { show: true },
                            points: { show: true }
                        },
                        grid: { hoverable: true, clickable: true },
                        xaxis: {
                            mode: "time",
                            timeformat: "%y %b",
                            tickSize: [1, "month"],
                            tickLength: 2
                        }
                    });
                });
            }(jQuery));
        </script>
        <% end_control %>
        <% control TotalBarData %>
        <h2><a href="{$Top.Link}cat/0"><% _t('TOTAL_PER_MONTH', 'Total per month') %></a></h2>
        <div class="chart" id="chart-total-bar"></div>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var data = $Data;
                    $.plot("#chart-total-bar", data, {
                        series: {
                            bars: {
                                show: true,
                                barWidth: 700000000,
                                align: "$Top.Align"
                            }
                        },
                        grid: { hoverable: true },
                        xaxis: {
                            mode: "time",
                            timeformat: "%y %b",
                            tickSize: [1, "month"],
                            tickLength: 2,
                            tickFormatter : function (val, axis) {
                                var label = $.plot.formatDate(new Date(val), "%y %b");
                                var link = $.plot.formatDate(new Date(val), "%y-%0m");
                                return '<a href="{$Top.Link}cat/0/' + link + '">' + label + '</a>';
                            }
                        }
                    });
                });
            }(jQuery));
        </script>
        <% end_control %>
        <% control CategoryData %>
        <h2><a href="{$Top.Link}cat/$Category.ID">$Category.Title</a></h2>
        <div class="chart" id="chart-$Id"></div>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var data = $Data;
                    $.plot("#chart-$Id", data, {
                        series: {
                            bars: {
                                show: true,
                                barWidth: 700000000,
                                align: "$Top.Align"
                            }
                        },
                        grid: { hoverable: true },
                        xaxis: {
                            mode: "time",
                            timeformat: "%y %b",
                            tickSize: [1, "month"],
                            tickLength: 2,
                            tickFormatter : function (val, axis) {
                                var label = $.plot.formatDate(new Date(val), "%y %b");
                                var link = $.plot.formatDate(new Date(val), "%y-%0m");
                                return '<a href="{$Top.Link}cat/$Category.ID/' + link + '">' + label + '</a>';
                            }
                        }
                    });
                });
            }(jQuery));
        </script>
        <% end_control %>
        $Form

        <% if PageComments %><section>$PageComments</section><% end_if %>
    </section>

    <!--<aside class="grid_4">-->
    <% include SideBar %>
    <!--</aside>-->

</div>
