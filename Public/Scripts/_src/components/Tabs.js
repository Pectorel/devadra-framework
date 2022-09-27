class Tabs
{

    constructor($el)
    {

        checkType(
            $el,
            "object",
            {
                functionName: "Popup",
                varName: "$popup"
            }
        );

        this.$tabs = $el;
        this.$tabsNav = this.$tabs.find("[data-tabs-nav]");
        this.$tabsLink = this.$tabsNav.find("[data-tabs-link]");
        this.$tabsContent = this.$tabs.find("[data-tabs-content]");
        this.$tabsBlocs = this.$tabsContent.find(">div");
        //console.log(this.$tabsBlocs);

        this.setTabsLinksEvents();


    }

    setTabsLinksEvents()
    {

        this.$tabsLink.each(function (i, el)
        {

            let $el = $(el);

            $el.on("click", function (event)
            {

                if(event) event.preventDefault();

                let $target = $("#" + $el.attr("data-tabs-link"));

                //console.log($target);

                this.$tabsLink.removeClass("active");
                $el.addClass("active");


                if(!empty($target))
                {

                    this.$tabsBlocs.removeClass("active");
                    $target.addClass("active");

                }

            }.bind(this));

        }.bind(this));

    }

}


$(window).ready(function ()
{
    let $data_tabs = $("[data-tabs]");


    $data_tabs.each(function ()
    {
        new Tabs($(this));
    });
});

