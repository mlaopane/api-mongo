# Slim API

## Dependencies
* PHP 7+
* MongoDB 1.1+
* [MongoDB Driver for PHP](https://pecl.php.net/package/mongodb)

## Framework
* [Slim 3](https://www.slimframework.com/) with the [MongoDB PHP library](https://docs.mongodb.com/php-library/current/tutorial/)

## Before we start...
***MongoDB*** belongs to the **NoSQL** Database Manager family.  
Let's make a simple comparison :

| MongoDB      |   MySQL    |
|--------------|------------|
| *database*   | *database* |
| *collection* | *table*    |
| *document*   | *row*      |
| *field*      | *column*   |

---

## Let's get started ! ^\_^

You can use it locally by :
1. Browsing to the project root
2. Opening your terminal
3. Using the following command to create a local server :  
`php -S localhost:<port> -t public`

Use any available port to replace the `<port>` placeholder.  
You may need to activate the MongoDB Driver extension by adding it to your php.ini file.

## Making HTTP requests

This API uses the following HTTP methods :
* **GET**
* **POST**
* **PUT**
* **PATCH**
* **DELETE**

Here is the URI pattern :  
`http://localhost:<port>/{database}[/{collection}[/{id}]]`

The brackets mean that the identifier inside the curly braces may be optional.
It actually depends on the request method as we will see further.

## The HTTP message body

The construction of the body is pretty simple.
Here is a generic possibility :

```json
{  
    "filter": {  
        "field": "value"  
    },  
    "data": [  
        {  
            "field": "value"  
        }  
    ]  
}
```

---

## Usage rules

### < POST Request >

The request body IS required.

Natively, the API provides two kinds of POST requests

#### *'Creation' request*

Create one/many document(s) for a given collection.  

*Pattern* : `/{database}/{collection}`

##### *Example*

```javascript
/** POST /forest/characters **/

/* Request body */
{
    "data": [
        {"name": "Toto", "class": "troll"},
        {"name": "Tata", "class": "elfe"},
        {"name": "Titi", "class": "elfe"}
    ]
}

/* Response body */
{
    "created": 3,
    "collectionName": "characters",
    "databaseName": "forest",
    "data": [
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Toto",
            "category": "troll"
        },
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Tata",
            "name": "elfe"
        },
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Titi",
            "name": "elfe"
        }
    ]
}
```

#### *'Search' request*

Search for documents in a given database or collection.

*Pattern* :  `/{database}[/{collection}]/_search`  

##### *Examples*

```javascript
/** POST /forest/_search **/

/* Request body */
{
    "filter": {
        "class": "elfe"
    }
}

/* Response body */
{
    "matched": 2,
    "collections" [
        "collectionName": "characters",
        "matched": 2,
        "data": [
            {
                "_id": {
                    "oid": // Already auto-generated unique id
                },
                "name": "Tata",
                "class": "elfe"
            },
            {
                "_id": {
                    "oid": // Already auto-generated unique id
                },
                "name": "Titi",
                "class": "elfe"
            }
        ]
    ]
}
```

```javascript
/** POST /forest/characters/_search **/

/* Request body */
{
    "filter": {
        "class": "troll"
    }
}

/* Response body */
{
    "matched": 1,
    "data": [
        {
            "_id": {
                "oid": // Already auto-generated unique id
            },
            "name": "Toto",
            "class": "troll"
        }
    ]
}
```

At some point, you may need advanced filters.  
Remember that the API is using MongoDB, therefore you are able to use any special operators provided by MongoDB.

Feel free to use the [MongoDB Documentation](https://docs.mongodb.com/manual/reference/) for any help.

##### *Examples*

```javascript
/**  Find documents with an "age" field greater than 18  **/
{
    "filter": {
        "age": {
            "$gt": 18
        }
    }
}
```

```javascript
/** Find documents without a "category" field  **/
{
    "filter": {
        "category": {
            "$exists": false
        }
    }
}
```


### < GET Request >

Fetch all the documents for a given resource

* *Pattern* : `/{database}[/{collection}[/{id}]]`
* The request body IS NOT used

##### *Examples*

```javascript
/**  GET /forest  **/

/* Response body */
{
    "count": 5
    "collections": [
        {
            "collectionName": "characters",
            "count": 3,
            "data": [
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Toto",
                    "class": "troll"
                },
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Tata",
                    "class": "elfe"
                },
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Titi",
                    "class": "elfe"
                }
            ]
        },
        {
            "collectionName": "trees",
            "count": 2,
            "data": [
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "apple",
                },
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "ceder",
                },
            ]
        }
    ]
}
```

```javascript
/**  GET /forest/trees  **/

/* Response body */
{
    "count": 2
    "data": [
        {
            "_id": [
                "oid": // Already auto-generated unique id
            ]
            "name": "apple",
        },
        {
            "_id": [
                "oid": // Already auto-generated unique id
            ]
            "name": "cedar",
        }
    ]
}
```

```javascript
/**  GET /forest/trees/<unique_id>  **/

/* Response body */
{
    "data": {
        "_id": {
            "oid": //...
        }
        "name": "cedar"
    }
}
```
