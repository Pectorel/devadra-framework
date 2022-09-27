class Equalizer {


    constructor($el, options)
    {

        checkType(
            $el,
            "object",
            {
                functionName: "Equalizer",
                varName: "$el"
            }
        );

        this.$container = $el;
        this.max_height = 0;
        this.equalized_blocks = this.$container.find("[data-equalize-block]");

        this.equalize();

    }


    equalize()
    {


        this.equalized_blocks.each(function (index, $el) {
            //console.log($($el).outerHeight());

            if($($el).outerHeight() > this.max_height)
            {

                this.max_height = $($el).outerHeight();

            }

        }.bind(this));


        this.equalized_blocks.outerHeight(this.max_height);


    }

}


// Default Behavior

$(window).ready(function ()
{
    // Default Behavior
    let $equalizers = $("[data-equalizer]");
    //console.log($equalizers);

    if(!empty($equalizers))
    {


        $equalizers.each(function ()
        {
            new Equalizer($(this));
        });


    }

});