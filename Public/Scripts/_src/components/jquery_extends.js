/* Permet de récupérer un élément dont un attr commence par une string donnée */
jQuery.extend(jQuery.expr[':'], {
    attrStartsWith: function (el, _, b) {
        for (let i = 0, atts = el.attributes, n = atts.length; i < n; i++) {
            if(atts[i].nodeName.toLowerCase().indexOf(b[3].toLowerCase()) === 0) {
                return true;
            }
        }

        return false;
    }
});