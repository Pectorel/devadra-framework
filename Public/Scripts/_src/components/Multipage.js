class Multipage {

    constructor($el, options)
    {

        checkType(
            $el,
            "object",
            {
                functionName: "Multipage",
                varName: "$el"
            }
        );

        this.$container = $el;

        this.optionsDefault =
        {
            animtype: "slide_left",
            callback: null
        };


        if(!empty(options))
        {
            for(let key in this.optionsDefault)
            {
                if(!empty(options[key]))
                {
                    this.optionsDefault[key] = options[key];

                }
            }
        }

        this.options = this.optionsDefault;

        this.index = 0;

        this.createContainer();

        this.setEvent();


    }

    createContainer()
    {

        let $children = this.$container.children();

        //console.log($children);

        this.$wrapper = $(document.createElement("div"));

        this.$container.append(this.$wrapper);

        //console.log(this.$wrapper);

        this.$wrapper.addClass("multi_wrapper");

        let page_width = this.$container.width();

        this.count = 0;

        $children.each(function (id, elem) {

            this.count++;

            $(elem).remove();

            this.$wrapper.append($(elem));
            $(elem).width(page_width);

        }.bind(this));


        let width = page_width * this.count;

        this.$wrapper.width(width);

        if(!empty(this.options.callback))
        {

            this.options.callback();

        }

    }

    goTo(index)
    {


        if(index >= 0 && index < this.count)
        {
            this.index = index;

            if(this.options.animtype === "slide_left")
            {
                this.$wrapper.css("left", -(100 * index) + "%");
            }
        }


    }

    next(){
        this.index++;

        if(this.index >= this.count)
        {
            this.index = 0;
        }

        this.goTo(this.index);
    }

    previous()
    {
        this.index--;

        if(this.index < 0)
        {
            this.index = 0;
        }

        this.goTo(this.index);
    }

    setEvent()
    {

        let $previous = this.$container.find("[data-previous]");

        $previous.each(function (id, elem) {


            $(elem).click(function (event) {

                if(event) event.preventDefault();

                this.previous();

            }.bind(this));

        }.bind(this));

        let $next = this.$container.find("[data-next]");

        $next.each(function (id, elem) {


            $(elem).click(function (event) {

                if(event) event.preventDefault();

                this.next();

            }.bind(this));

        }.bind(this));

    }

}

$(window).ready(function ()
{
    // Default Behavior
    let $multipages = $("[data-multipage]");

    if(!empty($multipages))
    {

        $multipages.each(function ()
        {
            new Multipage($(this));
        });

    }

});