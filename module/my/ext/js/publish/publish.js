$(function()
{   

    var cssheight=window.screen.height*0.85*0.65+"px";

    $('#featuresCarousel').css('height',cssheight);
    /* Support to slide to next by click btn */
    $('#features').on('click', '.slide-feature-to-next', function()
    {   
        $('#featuresCarousel').carousel('next');
    });

    $('#features').on('click', '.slide-feature-to-prev', function()
    {   
        $('#featuresCarousel').carousel('prev');
    });

    $('#features').on('click', '.btn-close-modal', function()
    {   
        var feature = features[features.length - 1];
    });

    $('#featuresCarousel').on('slide.zui.carousel', function(e)
    {   
        /* Click next to pause the video play. */

        var $next      = $(e.relatedTarget);
        var $items     = $next.parent().children();
        var index      = $items.index($next);
        var itemsCount = $items.length;
        var isLastItem = index === itemsCount - 1;
        var $features  = $('#features');
        var $nav = $('#featuresNav');
        $nav.find('li.active').removeClass('active');
        $nav.find('a[data-slide-to="' + index + '"]').parent().addClass('active');

        $features.toggleClass('is-last-item', isLastItem);
        if(isLastItem) $features.addClass('enabled');

        var feature = features[index - 1];
    });

    $('#features').toggleClass('is-last-item', $('#featuresCarousel>.carousel-inner>.item').length < 2);

    $('#closePublish').on('click',function()
    {   
        $.post(createLink('my', 'savepublish'));
    });
});
