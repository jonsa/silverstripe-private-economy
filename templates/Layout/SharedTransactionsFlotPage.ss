<div id="pageContent">
    <section class="grid_12">

        <h1>$Title</h1>

        $Content
        <% control TotalPointData %>
        <h2>Totalt</h2>
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
                        grid: { hoverable: true },
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
        <h2>Totalt per månad</h2>
        <div class="chart" id="chart-total-bar"></div>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var data = $Data;
                    $.plot("#chart-total-bar", data, {
                        series: {
                            bars: {
                                show: true,
                                barWidth: 700000000
                            }
                        },
                        grid: { hoverable: true },
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
        <% control CategoryData %>
        <h2>$CategoryTitle</h2>
        <div class="chart" id="chart-$Id"></div>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var data = $Data;
                    $.plot("#chart-$Id", data, {
                        series: {
                            bars: {
                                show: true,
                                barWidth: 700000000
                            }
                        },
                        grid: { hoverable: true },
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
        $Form

        <% if PageComments %><section>$PageComments</section><% end_if %>
    </section>

    <!--<aside class="grid_4">-->
    <% include SideBar %>
    <!--</aside>-->

</div>
