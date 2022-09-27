let fs = require('fs');
let fileName = './manifest.json';
let file = require(fileName);

// On récupère les arguments de la commande (on enlève les deux premiers car ce sont node et versioning.js)
let args = process.argv.slice(2);


//console.log(args);

// Si aucun argument, alors on update tout de 1
if(args.length === 0)
{

    for(let css in file.versioning.css)
    {

        file.versioning.css[css]+=1;

    }

    for(let js in file.versioning.js)
    {

        file.versioning.js[js]+=1;

    }

}
// Sinon on update le fichier en particulier
else{

    // js ou css
    let changingFile = args[0];

    // public ou admin
    let changingType = args[1];

    // On vérifie que la propriété existe bien
    if(file.versioning[changingFile][changingType])
    {
        file.versioning[changingFile][changingType]+=1;
    }
    else{
        console.log("Index introuvable dans le manifest.json, vérifier la commande");
    }

}

// On écrit toutes les modifications dans le fichier manifest.json
fs.writeFile(fileName, JSON.stringify(file, undefined, 2), function (err) {
    if (err) return console.log(err);
    //console.log(JSON.stringify(file));
    //console.log('writing to ' + fileName);
});