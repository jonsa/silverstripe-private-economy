<div id="pageContent">
    <section class="grid_12">

        <h1>$Title<% if Category %> $Category.Title <% end_if %><% if Range %> $Range<% end_if %></h1>

        $Content
        <% if Users %>
        <% control Users %>
        <h2>$Name</h2>
        <table class="overview">
            <thead>
                <tr>
                    <th class="first"><div><% _t('TRANSACTION_DATE', 'Transaction Date') %></div></th>
                    <th class="third"><div><% _t('CATEGORY', 'Category') %></div></th>
                    <th class="second number"><div><% _t('AMOUNT', 'Amount') %></div></th>
                    <th><div><% _t('COMMENT', 'Comment') %></div></th>
                </tr>
            </thead>
            <tbody>
                <% control Transactions %>
                <tr class="$EvenOdd<% if Last %> last<% end_if %>">
                    <td><div>$TransactionDate</div></td>
                    <td><div>$Category.Title</div></td>
                    <td class="number"><div>$Amount</div></td>
                    <td><div>$Comment</div></td>
                </tr>
                <% end_control %>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="2"><div><% _t('TOTAL', 'Total') %></div></td>
                    <td class="number"><div>$Total</div></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <p></p>
        <% end_control %>
        <% else %>
        <table class="overview">
            <thead>
            <tr>
                <th class="first"><div><% _t('TRANSACTION_DATE', 'Transaction Date') %></div></th>
                <th class="third"><div><% _t('CATEGORY', 'Category') %></div></th>
                <th class="second number"><div><% _t('AMOUNT', 'Amount') %></div></th>
                <th><div><% _t('COMMENT', 'Comment') %></div></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="4"><div><% _t('NO_EXPENSES', 'No expenses during this period') %></div></td>
            </tr>
            </tbody>
        </table>
        <% end_if %>
        $Form

        <% if PageComments %><section>$PageComments</section><% end_if %>
    </section>

    <!--<aside class="grid_4">-->
    <% include SideBar %>
    <!--</aside>-->

</div>
