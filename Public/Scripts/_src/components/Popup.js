class Popup{

    constructor($popup, options)
    {


        //console.log($popup);

        checkType(
            $popup,
            "object",
            {
                functionName: "Popup",
                varName: "$popup"
            }
        );

        this.$popup = $popup;

        this.optionsDefault =
            {
              animtype: "slide_top"
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

        if(!empty(this.options.animtype))
        {
            this.$popup.addClass(this.options.animtype);
        }

        this.setPopupLinksEvent();

        this.$popupLayer = $("#PopupLayer");


    }

    setPopupLinksEvent()
    {

        this.selector = "[data-popup-link=" + this.$popup.attr("id") + "]";

        $(document).on("click", this.selector, function (event)
        {


            if(!empty(event)) event.preventDefault();
            this.open();


        }.bind(this));

        $(document).on("click", "[data-popup-close]", function (event)
        {

            if(event) event.preventDefault();
            this.close();

        }.bind(this))




    }

    open(){

        this.$popup.addClass("active");
        this.$popupLayer.addClass("active");

    }

    setAnimationType(type){


        if(!empty(type))
        {

            this.$popup.removeClass(this.options.animtype);
            this.options.animtype = type;
            this.$popup.addClass(this.options.animtype);
        }

    }

    close(){

        this.$popup.removeClass("active");
        this.$popupLayer.removeClass("active");

    }

}

$(window).ready(function ()
{
    // Default Behavior
    let $popups = $("[data-popup]");

    if(!empty($popups))
    {

        $popups.each(function ()
        {
            new Popup($(this));
        });

    }

});

