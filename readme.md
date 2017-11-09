# Curriculum Vitae API

### Database
* cvtheque

### Collections
* identity
* skill
* experience
* training

### Dependencies
* MongoDB

### Framework
* Slim with the MongoDB library for PHP

### Before we start...
MongoDB belongs to the **NoSQL** Database Manager family.
To make a simple comparison with **MySQL** :

| [MongoDB] | [MySQL] |
| *database* | *database* |
| *collection* | *table* |
| *document* | *row* |
| *field* | *column* |

---

### Let's get started

You can use it locally by :
1. Browsing to the project root
2. Opening your terminal
3. Using the following command to create a local server : `php -S localhost:8000 -t public`

### Making HTTP requests

This API uses the common HTTP methods :
* *GET*
* *POST*
* *PUT*
* *DELETE*

Here is the URI pattern : `http://localhost:8000/{database}[/{collection}[/{id}]]`

The brackets mean that the identifier inside the curly braces may be optional.
It actually depends on the request method as we will see further.

### The HTTP message body

The construction of the body is pretty simple.
Here is a generic possibility :
`{  
    "filter": {  
        "field": "value"  
    },  
    "data": [  
        {  
            "field": "value"  
        }  
    ]  
}`

The API follow these rules :
* You **MAY** use the **filter** key for ***GET***, ***POST***, ***DELETE*** requests
* The **filter** key for ***PUT*** request has no effect
* The **filter** key for ***DELETE*** request has no effect if you provide a document *id*
* You **MUST** use the **data** key for ***POST***, ***PUT*** requests
* You **MAY** use the **data** key for ***GET***, ***DELETE*** requests
