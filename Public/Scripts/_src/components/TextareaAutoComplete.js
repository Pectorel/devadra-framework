class TextareaAutoComplete
{


    constructor($elem, options)
    {

        checkType($elem, "object",{varName: "$elem", functionName: "TextareaAutoComplete::constructor"});

        this.$elem = $elem;

        this.optionsDefault = {
            json: {},
            list: {
                position: "bottom",
                limit: 10
            }
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

        this.createAutocompleteDiv();

        if(!empty(this.options.json))
        {
            this.setAutocomplete(this.options.json);
        }

        this.setListenerEvent();


    }

    createAutocompleteDiv()
    {

        this.div = document.createElement("div");

        this.div.className = "autocomplete_block d_none " + this.options.list.position;

        this.$elem.after(this.div);


    }

    setAutocomplete(json)
    {

        this.autocomplete = json;

    }

    getAutocomplete(text)
    {


        let json = {};

        if(text.length < 1 || empty(text))
        {

            let j = 0;

            for(let i in this.autocomplete)
            {

                if(j < this.options.list.limit)
                {
                    json[j] = this.autocomplete[i];
                }
                else
                {
                    break;
                }

                j++;
            }

        }
        else
        {
            let j = 0;
            for(let i in this.autocomplete)
            {

                if(j < this.options.list.limit)
                {

                    if(this.autocomplete[i].indexOf(text) === 0)
                    {
                        json[j] = this.autocomplete[i];
                    }

                }
                else
                {
                    break;
                }

                j++;
            }
        }

        return json;


    }


    setListenerEvent()
    {

        $("body").on("input", this.$elem, function ()
        {


            setTimeout(function () {
                let text = this.$elem.val();

                let last_word = text.replace(/(<([^>]+)>)/ig,"").slice(-2);

                console.log(text);

                if(last_word == "::")
                {

                    if(!this.opened)
                    {
                        this.opened = true;
                    }
                    else
                    {
                        this.opened = false;
                    }
                }

                if(this.opened)
                {

                    last_word = last_word.replace(/:/g, "");

                    let results = this.getAutocomplete(last_word);

                    $(this.div).empty();
                    console.log(results);
                    if(empty(results))
                    {
                        $(this.div).addClass("d_none");
                    }
                    else
                    {

                        for(let k in results)
                        {

                            let newLine = "<span class='autocomplete_line " + results[k].replace(" ", "_") + "'>" + results[k] + "</span>";

                            let $newLine = $(this.div).append(newLine);

                            let text = results[k];

                            $($newLine).click(function () {


                                let html = this.$elem.trumbowyg("html");

                                let regexp = new RegExp("</([^>]+)>", "ig");

                                regexp.test(html);

                                let matches = html.match(/<\/([^>]+)>/ig);


                                if(!empty(matches))
                                {

                                    let lastmatch = matches[matches.length-1];
                                    console.log(lastmatch);

                                    let removed_tag = lastmatch;

                                    console.log("removed tag", removed_tag);

                                    let parseHtml = html.slice(0, html.length - lastmatch.length);

                                    console.log("Parse HTML", parseHtml);

                                    this.$elem.trumbowyg("empty");
                                    this.$elem.trumbowyg("html",  parseHtml + text + "::" + removed_tag);

                                }
                                else
                                {
                                    this.$elem.trumbowyg("html",  this.$elem.trumbowyg("html") + text + ":: ");
                                }






                                this.opened = false;
                                $(this.div).empty();
                                $(this.div).addClass("d_none");

                            }.bind(this));

                        }

                        let coordinates = getCaretCoordinates(document.getElementById(this.$elem.attr("id")), this.$elem.prop("selectionEnd"));

                        $(this.div).css("left", coordinates.left + 10 + "px");
                        $(this.div).css("top", coordinates.height + 30 + "px");

                        $(this.div).removeClass("d_none");

                    }


                }
            }.bind(this), 200);





        }.bind(this));

    }

}