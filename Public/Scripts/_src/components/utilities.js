/**
 * Fonction qui vérifie si une variable est vide ou non
 *
 * @param {*} data
 * @returns {boolean}
 */
function empty(data)
{

    let cond = (typeof data === "undefined" || data === null || data === 0 || data === false);

    if (!cond)
    {

        if (data.constructor === Array || data.constructor === String)
        {
            cond = data.length <= 0
        }
        else if (data.constructor === Object)
        {
            cond = (Object.keys(data).length <= 0);
        }

    }

    return cond;

}


/**
 *
 * Check le type d'une variable donné et throw une erreur si le type n'est pas le bon
 *
 * @param $el
 * @param expectedtype
 * @param options
 */
function checkType($el, expectedtype, options){


    if(typeof $el !== expectedtype)
    {

        let mess = options.functionName + " expected " + options.varName + " to be " + expectedtype + ", " + typeof $el + " given instead";
        throwErr(mess);

    }


}

function throwErr(mess, type)
{

    if(!empty(type))
    {


        switch (type)
        {

            case "warn":
                console.warn(mess);
                return;
                break;

        }


    }

    throw new Error(mess);

}