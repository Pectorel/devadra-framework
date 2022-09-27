jQuery(function($) {


    function setTrumbowyg()
    {

        $.trumbowyg.svgPath = "/node_modules/trumbowyg/dist/ui/icons.svg";


        // Textarea editor
        $("textarea").trumbowyg({
            removeformatPasted: true,
            lang: "fr",
            btns: [
                ['undo', 'redo'], // Marche pas sur Edge / IE
                ['formatting'],
                ['strong', 'em', 'del', 'underline'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ]
        });

    }

    setTrumbowyg();



// Datetime Pickers

    let $datetimepickers = $("input[type=datetime]");

    jQuery.datetimepicker.setLocale("fr");

//console.log($datetimepickers);

    $datetimepickers.each(function () {
        $(this).datetimepicker({
            dayOfWeekStart: 1,
            step: 30,
            formatTime: 'H:i:s'
        });

    });


    // AddAjax

    $("[id^='AddAjaxButton']").on("click", function (event) {

        if (event) event.preventDefault();

        let controller = $(this).attr("data-controller");

        let url = controller + "/addAjax" + "?noSave&noRemovePopup&localRemove";
        let containerString = "#" + controller + "FormsContainer";
        let $container = $(containerString);


        $.get({
            url : url,
            success: function (data) {
                //console.log(data);

                $container.append(data);
                setTrumbowyg();

            }
        })

    });




    let promiseCareDepent = new Promise(function (resolve, reject) {

        let i = 0;
        let forms = [];
        let $data_care = $("[data-care-depent]");
        let allCareDepent = $data_care.length;
        // CareDepent Form Generator
        let j = 0;
        $data_care.each(function (index) {

            //console.log("test");
            ///debugger;



            let controller = $(this).attr("data-cd-controller");
            let id = $(this).attr("data-cd-id");

            let url = controller + "/modifyAjax?id=" + id + "&noSave&delButton&noRemovePopup";

            //let containerString = "#" + controller + "FormsContainer";
            //let $container = $(containerString);


            $.get({
                url : url,
                success: function (data) {

                    //console.log(data);

                    if(empty(forms[controller]))
                    {
                        forms[controller] = [];
                        j = 0;
                    }

                    //console.log(j);
                    forms[controller][j] = data;
                    j++;
                    i++;

                    if(i == allCareDepent)
                    {

                        //console.log($container);

                        //forms["container"] = $container;

                        resolve(forms);

                    }

                    //console.log(forms);



                }.bind(this)
            })


        });

    });

    promiseCareDepent.then(function (forms) {

        console.log(forms);

        for(let k in forms)
        {
            if(forms.hasOwnProperty(k))
            {
                let containerString = "#" + k + "FormsContainer";
                let $container = $(containerString);
                console.log($container);

                for(let z = 0; z < forms[k].length; z++)
                {

                    let current_elem = forms[k][z];
                    let $elem = $container.append(current_elem);
                    $(this).remove();
                    setTrumbowyg();
                    setAjaxCall($elem.find("[data-ajax-url]"));
                }
            }
        }
    });





    $("#RemoveConfirmLink[data-ajax]").each(function () {
        //console.log($(this));

        $(this).on("click", function (event) {

            if(event) event.preventDefault();
            let url = $(this).attr("href");

            $.post({
                url: url,
                success: function (data) {

                    $("#RemoveConfirm").removeClass("active");

                    let $confirmPopup = $("#ConfirmAjaxPopup");
                    $confirmPopup.find("#ConfirmAjaxText").text(data.ErrMess);
                    $confirmPopup.addClass("active");


                    if(data.success)
                    {

                        let $input = $("input[name='id'][value=" + data.id + "]");
                       // console.log($input);
                        //debugger;

                        let $section = $input.closest("section");
                        //console.log($section);

                        $section.remove();


                    }

                }
            })

        });

    });

    //LocalRemove
    $(document).on("click", ".local_remove", function (event) {

        if(event) event.preventDefault();
        console.log($(this).closest("section"));

        $(this).closest("section").remove();

    });


    $("body").on("change", ".custom_file_button", function () {

        let $filename = $(this).siblings(".filename");
        let $phrase = $(this).siblings(".add_phrase");

        $filename.text(this.value.split(/(\\|\/)/g).pop());

        $phrase.text("Modifier l'image");



    });
    

});

// RemoveListingAdmin
function listingAdminRemove(data) {


    let $removeConfirm = $("#RemoveConfirm");
    $removeConfirm.removeClass("active");

    let $confirmPopup = $("#ConfirmAjaxPopup");
    $confirmPopup.find("#ConfirmAjaxText").text(data.ErrMess);
    $confirmPopup.addClass("active");

    if(data.success)
    {

        let $line = $(".item_line[data-id=" + data.id +"]");

        if(!empty($line))
        {
            $line.remove();
        }

    }

}

function remImageCallback(data) {

    let $removeConfirm = $("#RemoveConfirm");
    $removeConfirm.removeClass("active");

    let $confirmPopup = $("#ConfirmAjaxPopup");
    $confirmPopup.find("#ConfirmAjaxText").text(data.ErrMess);
    $confirmPopup.addClass("active");

    if(data.success)
    {


        let $img = $("[src='./Public/Images/" + data.controller + "/" + data.id + "/" + data.index + "." + data.type + "']");

        let $container = $img.parent();

        let parent = $container.parent();

        if(!empty($container))
        {
            $container.remove();
        }

        let elem = "<label class=\"image_input_container txt_center\">\n" +
"                       <span>Ajouter une image</span>\n" +
"                       <input id=\"Image" + data.controller + data.index + "\" class=\"custom_file_button\" type=\"file\" name=\"images[]\">\n" +
"                   </label>";

        parent.append(elem);



    }

}