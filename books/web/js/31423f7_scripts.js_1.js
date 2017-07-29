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

$(document).ready(function () {


    function formatRepo (repo) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.title + ": " +repo.subtitle +"</div>";

        markup += "<div class='select2-result-repository__description'>" + repo.author + "</div></div></div>";

        return markup;
    }

    function formatRepoSelection (repo) {
        return repo.text;
    }

    $(".openLibrarySelectAjax").select2({
        ajax: {
            url: Routing.generate('search_open_library'),
            dataType: 'json',
            delay: 400,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        allowClear: true,
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 4,
        placeholder: "Search in openlibrary.org",
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    }).on('select2:selecting', function(e) {
        var obj = e.params.args.data;
        console.log(obj);
        $('.bookTitleClass').val(obj.title);
        $('.bookSubtitleClass').val(obj.subtitle);
        $('.bookAuthorClass').val(obj.author);
        var description = obj.title+ ": "+obj.subtitle+"\n";
        description += obj.author.toString() + "\n";
        description += "\n\n" + obj.person.toString();
        description += "\n\n" +obj.place.toString();
        description += "\n\n" + obj.time.toString();
        $('.bookDescriptionClass').val(description);

    });

});
