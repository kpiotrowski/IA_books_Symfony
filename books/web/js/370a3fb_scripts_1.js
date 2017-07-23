$(document).ready(function () {
    var pageNum = $('#pageNum');

    $('.tablePrevButton').click(function () {
        var val = pageNum.val();
        if (val > 1) {
            val--;
            pageNum.val(val);
            $('#booksForm').submit();
        }
    });
    $('.tableNextButton').click(function () {
        var val = pageNum.val();
        if (val < $('#maxPages').val() ) {
            val++;
            pageNum.val(val);
            $('#booksForm').submit();
        }
    });
});
