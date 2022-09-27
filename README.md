# SpellbreakBuilder

---

## Installation

---

### Prerequisite

* Apache Server ([WAMP](http://www.wampserver.com/), etc.)
* [Node.js](https://nodejs.org/en/)
* [Sass](https://sass-lang.com/) ([chocolatey](https://chocolatey.org/) recommanded)

### Étapes

1. Clone project

2. Run ``npm install``

3. Run ``npm run installUtilities`` and change your PATH environnement var otherwise the css et js minify won't work

4. [Create a vhost](http://forum.wampserver.com/read.php?2,146746) on your local server


## Introduction

---

### DevadraFramework

The Devadra Framework is a light PHP framework based on what I learned with [Zend Framework](https://framework.zend.com/). 
Let me show you how it works.

#### Creating a new Page

##### The Controller

Every Pages are created in a Controller, they are located in the **/controllers** folder:
```php 
<?php
    class IndexController extends Controller
    {
           
    }
```
Controller is currently empty, let's add a "Hello World" page :
```php 
<?php
    class IndexController extends Controller
    {
           
       public function helloWorldAction()
       {
            
       }
           
    }
```
In order to make new pages, we create what I call "Actions", 
every page are called in a Controller using this syntax =>*nameOfThePage*Action.

##### The View

Now that we have the back-end, we need the front! I call it a "View". 
All views are located in the **/views** folder, then let's create a file in **/views/index** 
called **helloWorld.php** (this is case sensitive)

```php 
<?php
    echo "Hello World";
?>
```
If you try to go on **/Index/helloWorld** right now you will get a 404 error, do not worry, this is intended.
We still have one file to modify.

##### The ACL
The Acl (Access Control List) is an xml file that deals with users permissions to visit a page or use a script.

```xml 
<?xml version="1.0" encoding="UTF-8"?>
<acl>
    <roles>
        <guest>
            <Index>
                <functions>
                    <index>
                        <right>allow</right>
                    </index>
                </functions>
            </Index>
        </guest>
    </roles>
</acl>
```

Here's an example of an ACL, there are multiple roles. In our example, we're gonna need to modify the guest role, 
which is the default role when a user have none. Let's add a new page called **helloWorld**

```xml 
<?xml version="1.0" encoding="UTF-8"?>
<acl>
    <roles>
        <guest>
            <Index>
                <functions>
                    <index>
                        <right>allow</right>
                    </index>
                    <helloWorld>
                        <right>allow</right>
                    </helloWorld>
                </functions>
            </Index>
        </guest>
    </roles>
</acl>
```

Now if you try again to go on **Index/HelloWorld**, you should have a nice "Hello World" message!

#### Assign vars to the view

##### Controller

 It is rather simple to assign vars to the view, let's go back into the IndexController :

```php 
<?php
    class IndexController extends Controller
    {
           
       public function helloWorldAction()
       {
            $this->view->text = "Hello World but Variable";
       }
           
    }
```

And that's it! To assign a var to the view, you just need to write this
``this->view->var_name = value``.

##### View

Now we need to get this var in our view :

```php 
<?php
    echo $this->text;
?>
```

Et voilà! If you go back on your page, you should see that the text has changed.

#### Models

Models are located in the **/models** folder, they allow our app to communicate with a database.
Every table that are in the DB should have a Model, preferably the same name as the table.

```php 
<?php
    class Item extends LangModel
    {
        
        protected $_instance;
        protected $_table = "Item";
        
        protected $_referenceMap = array(
            "TypeItem" => array(
                "table" => "TypeItem",
                "foreign" => "TypeItem_id",
                "constraint" => "id",
                "showColumn" => "libelle"
            ),
            "Language" => array(
                "table" => "Language",
                "foreign" => "Language_id",
                "constraint" => "id",
                "showColumn" => "nom"
            ),
        );
        
        protected $_careDepent = array();
    
        public function gestionForAction($obj)
        {
            $type = $this->findParent("TypeItem", $obj);
            return $type["libelle"];
        }
    
        public function getDisplayName($obj)
        {
            $res = null;
    
            if(is_array($obj))
            {
                $res = array_slice($obj, 1, 1)[0];
            }
    
            return $res;
        }
    
    }
``` 
Models and Controllers are generated with the setup script (located in **/install**), although you may need to add some after the initial setup of your project
you just need to run again the install script (only step 1 and 2) and you're good to go.
 
*Note: in the example below, the class inherits from LangModel, this is an in-between class which inherits from the Model class, it is use to deals with multiple languages*
 
##### Properties

``$_table`` Indicates the table to search in the DB

``$_referenceMap`` Map the foreign keys used in the table

``$_careDepent`` Allow to add another table that is linked to this one in the Admin back-office
 
##### Methods
 
``Model->fetchAll()`` Get all rows from the table

``Model->find($id)`` Get one row based on the ID provided

``Model->select($sql, $params, $fetch_type : PDO::FETCH_ASSOC, $fetch_style = "fetchAll")`` Make a SQL request with parameters *fetch_type* allows to choose the output format and *fetch_style* is used to indicate if we want one row or all rows

``Model->insert($insert_array)`` Add a record into the DB
 
 
#### Front-End

##### Style

Stylyng is made in [Sass](https://sass-lang.com/), I advise you to install it with [Chocolatey](https://chocolatey.org/) to upgrade it easily.
You're then going to need a Sass watcher (it is built-in in PHPStorm) or you can use the sass cmd commands to do it.

When you compile your Sass into CSS, you need to use the npm command UglifyCss from the package.json.
``npm run UglifyCss``

##### Javascript

Just write JS as you normally do, then compile it with``npm run buildJS`` it compile your js with BabelJs.