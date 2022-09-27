class AjaxCall {

    constructor($el, options)
    {

        checkType($el,
            "object",
            {
                functionName: "AjaxCall::constructor",
                varName: "$el"
            }
        );

        this.$el = $el;

        this.$el.on("click", function (event) {

            if(event) event.preventDefault();

            this.doRequest();


        }.bind(this));

        let instance_options = {
            "type": "GET",
            "url": null,
            "data": null,
            "callback": function(result){
                this.defaultCallback(result);
            }.bind(this),
            "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
            "target" : null
        };


        for(let i in options)
        {
            if(instance_options[i] !== undefined)
            {

                if(i === "callback")
                {
                    let functioname = options.callback;

                    instance_options.callback = function (result) {

                        //console.log(functioname);
                        window[functioname](result);
                    }
                }
                else if(i !== "target")
                {
                    instance_options[i] = options[i];
                }

                if(i === "target")
                {

                    instance_options.target = options.target;

                    if(!empty(options.callback))
                    {

                        instance_options.callback = function (result) {

                            //console.log(functioname);
                            this.appendCallback(result).then(function ($test) {
                                window[options.callback]($test);
                            });


                        }.bind(this);

                    }
                    else{
                        instance_options.callback = function(result){
                            this.appendCallback(result);
                        }.bind(this);
                    }




                }


            }
        }



        this.options = instance_options;

        //console.log((this.options));
        /*console.log(this.options);
        console.log(options);*/

    }


    doRequest()
    {


        let url = null;
        if(empty(this.options.url))
        {
            url = this.$el.attr("data-ajax-url");
        }
        else{
            url = this.options.url;
        }

        if(empty(url))
        {
            throwErr("Pas d'url configurée pour l'appel AJAX!");
        }


        let ajax_options =
            {
                type: this.options.type,
                url: url,
                contentType: this.options.contentType,
                success: function (data) {
                    this.options.callback(data);
                }.bind(this)
            };

        if(!empty(this.options.data))
        {
            ajax_options.data = this.options.data;
        }

        $.ajax(
            ajax_options
        );

    }


    defaultCallback(result)
    {
        console.log(result);
    }


    appendCallback(result)
    {

        let target = this.options.target;

        return new Promise(resolve => {
            let $test = $(target).append(result);
            resolve($test);
        });

    }



}

// Default Behaviour
function setAjaxCall($els)
{

    //console.log($els);

    $els.each(function () {

        let $el = $(this);

        let options = {};
        /*let options = {
            url: $el.attr("data-ajax-url")
        };*/

        if(!empty($el.attr("data-ajax-method")))
        {
            options.type = $el.attr("data-ajax-method");
        }

        if(!empty($el.attr("data-ajax-callback")))
        {
            options.callback = $el.attr("data-ajax-callback");
        }

        if(!empty($el.attr("data-ajax-vals")))
        {
            options.data = JSON.parse($el.attr("data-ajax-vals"));
        }

        if(!empty($el.attr("data-ajax-target")))
        {
            options.target = $el.attr("data-ajax-target");
        }

        //console.log(options.data);

        new AjaxCall($el, options);


    });
}

setAjaxCall($("[data-ajax-url]"));