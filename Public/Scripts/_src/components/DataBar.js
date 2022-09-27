class DataBar {

    constructor($el)
    {

        this.$el = $el;

        this.$el.append("<div class='value'></div>");

        this.max = $el.attr("data-bar-max");
        this.val = $el.attr("data-bar-val");

        this.width = this.calculateWidth() + "%";


        this.$value = this.$el.find(".value");

        this.$value.css("width", this.width);

        this.$value.append("<span>" + this.val + "</span>");

    }

    calculateWidth()
    {

        return (this.val/this.max)*100;

    }
}


function setDataBar($els) {


    $els.each(function () {

        new DataBar($(this));

    });

}