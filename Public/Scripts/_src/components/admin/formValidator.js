class FormValidator {

    constructor($form, options, e)
    {


        if(e) e.preventDefault();

        this.$form = $form;



        checkType(
            $form,
            "object",
            {
                functionName: "formValidator::constructor",
                varName: "$form"
            }
        );

        this.$forms = $form.find("form");



        /*console.log(this.$form);
        console.log(this.$forms);
        */

        /*
        this.last_id = null;

        if(!empty(this.$form.attr("data-id")))
        {

            this.last_id = this.$form.attr("data-id");

        }

        this.postForm(this.$form);
        */


        let payload = {};
        // On initialize avec le formulaire initial
        let formdata = this.getJsonSerialize(this.$form);




        //console.log(formdata);

        payload[this.$form.attr("data-controller")] = formdata;




       // debugger;


        this.$forms.each(function (id, value) {

            let cur_form = $(value);

            let parent_div = cur_form.parent("div");

           // debugger;

            let controller = cur_form.attr("data-controller");
            let index = 0;

            if(empty(payload[controller]))
            {
                payload[controller] = {};

            }
            else{
                index = Object.keys(payload[controller]).length;
            }

            let formdata = this.getJsonSerialize(cur_form);





            payload[controller][index] = formdata;










        }.bind(this));

        //console.log(payload);




        //console.log(payload);



        let PayloadData = Truthy(payload);


        //PayloadData = new FormData($("form")[0]);




        //throwErr("test");
        $.post({
            url: this.$form.attr("action"),
            data: PayloadData,
            //contentType : "application/x-www-form-urlencoded",
            contentType: false,
            processData: false,
            cache: false,
            success: function (data) {
                window.location.href = this.$form.attr("data-controller") + "/admin";
            }.bind(this)


        });


        /*this.$forms.each(function () {

            //if(!empty())

        });
*/



    }

    validForm(form)
    {

    }


    /*postForm($form)
    {



        let url = $form.attr("action");
        let data = $form.serializeArray();

        console.log(url);
        console.log(data);
        debugger;


        $.post(
            {
                url: url,
                data: data,
                success: function (data) {

                    if(data.success)
                    {

                        if(empty(this.last_id) && data.id)
                        {
                            this.last_id = data.id
                        }

                    }

                }.bind(this)
            }.bind(this)
        );

    }*/


    getJsonSerialize(form)
    {

        let json = {};



        let formdata = form.serializeArray();



        $.each(formdata, function () {

            json[this.name] = this.value;

        });

        json["images"] = {};

        let i = 0;

        form.children("#ImagesContainer").each(function () {

            $(this).find(".custom_file_button").each(function () {

                //console.log(this);




                json["images"][i] = this.files[0];

                i++;

            });

        });




        return json;

    }



}

function Truthy(obj, form, namespace) {
    let fd = form || new FormData();
    let formKey = void 0;
    for (let property in obj) {
        if (obj.hasOwnProperty(property) && obj[property]) {
            if (namespace) {
                formKey = namespace + '[' + property + ']';
            } else {
                formKey = property;
            }

            if (obj[property]instanceof Date) {
                fd.append(formKey, obj[property].toISOString());
            } else if (_typeof(obj[property]) === 'object' && !(obj[property]instanceof File)) {
                Truthy(obj[property], fd, formKey);
            } else {
                // if it's a string or a File object

                if(obj[property]instanceof File)
                {
                    //console.log(obj[property]);
                    //formKey = formKey.split("[images]")[0];

                    fd.append(formKey, obj[property]);
                }
                else{
                    //console.log(formKey);
                    fd.append(formKey, obj[property]);
                }


            }
        }
    }
    return fd;
}
