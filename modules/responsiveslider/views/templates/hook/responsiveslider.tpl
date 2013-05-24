{if $sliders|count > 0}
    <div id="featured">
        {foreach from=$sliders item=slider name=listSlider}
            <div data-caption="#caption{$smarty.foreach.listSlider.iteration}">
                {if $slider->url}
                    <a href="{$slider->url}">
                        <img src="{$modules_dir}responsiveslider/images/{$slider->urlimage}" alt="{$slider->title}"/>
                    </a>
                {else}
                    <img src="{$modules_dir}responsiveslider/images/{$slider->urlimage}" alt="{$slider->title}"/>
                {/if}
            </div>
        {/foreach}
    </div>

    {foreach from=$sliders item=slider name=listSlider}
        <span class="orbit-caption" id="caption{$smarty.foreach.listSlider.iteration}">{$slider->title}</span>
    {/foreach}

    <script type="text/javascript">
        $(window).load(function() {
            $('#featured').orbit({
                animation: '{$configuration['RESPONSIVESLIDER_ANIMATION']}',                  // fade, horizontal-slide, vertical-slide, horizontal-push
                animationSpeed: {$configuration['RESPONSIVESLIDER_ANIMATIONSPEED']},                // how fast animtions are
                timer: true,                        // true or false to have the timer
                resetTimerOnClick: false,           // true resets the timer instead of pausing slideshow progress
                advanceSpeed: {$configuration['RESPONSIVESLIDER_SLIDESHOWSPEED']},                 // if timer is enabled, time between transitions
                pauseOnHover: false,                // if you hover pauses the slider
                startClockOnMouseOut: false,        // if clock should start on MouseOut
                startClockOnMouseOutAfter: 1000,    // how long after MouseOut should the timer start again
                directionalNav: true,               // manual advancing directional navs
                captions: true,                     // do you want captions?
                captionAnimation: 'fade',           // fade, slideOpen, none
                captionAnimationSpeed: 800,         // if so how quickly should they animate in
                bullets: {if $configuration['RESPONSIVESLIDER_CONTROLNAV'] == 1}true{else}false{/if},                     // true or false to activate the bullet navigation
                bulletThumbs: false,                // thumbnails for the bullets
                bulletThumbLocation: '',            // location from this file where thumbs will be
                afterSlideChange: function(){},     // empty function
                fluid: true                         // or set a aspect ratio for content slides (ex: '4x3')
            });
        });
    </script>
{/if}