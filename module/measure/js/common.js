$(".btn-reset-form").click(function () {
    $(".searchForm .input-group").each(function () {
        $(this).children().eq(1).val('').trigger('chosen:updated')
    })
})