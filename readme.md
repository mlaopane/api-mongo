# Slim API

## Dependencies
* PHP 7+
* MongoDB 1.1+
* [MongoDB Driver for PHP](https://pecl.php.net/package/mongodb)

## Framework
* [Slim 3](https://www.slimframework.com/) with the [MongoDB PHP library](https://docs.mongodb.com/php-library/current/tutorial/)

## Before we start...
***MongoDB*** belongs to the **NoSQL** Database Manager family.  

A simple comparison :

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
Alternatively, if you have *npm* installed :  
`npm run server`  

You may need to activate the MongoDB Driver extension by adding it to your php.ini file.

## Making HTTP requests

This API uses the following HTTP methods :
* **GET**
* **POST**
* **PUT**
* **PATCH**
* **DELETE**

Here is the URI pattern :  
`http://localhost:<port>/{database:[a-z0-9_]+}[/{collection:[a-z0-9_]+}[/{id:[a-z0-9]+}]]`

The brackets mean that the identifier inside the curly braces may be optional.
It actually depends on the request type as we will see further.

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

### POST Request

The request body IS required.

Natively, the API provides two kinds of POST requests

#### *'Creation' request*

Create one/many document(s) for a given collection.  

*Pattern* : `/{database:[a-z0-9_]+}/{collection:[a-z0-9_]+}`

##### *Example*

```javascript
/** POST /forest/characters **/

/* Request body */
{
    "data": [
        {"name": "Toto", "race": "troll"},
        {"name": "Tata", "race": "elfe"},
        {"name": "Titi", "race": "elfe"}
    ]
}

/* Response body */
{
    "created": 3,
    "name": "characters",
    "databaseName": "forest",
    "data": [
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Toto",
            "race": "troll"
        },
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Tata",
            "race": "elfe"
        },
        {
            "_id": {
                "oid": // Auto-generated unique id
            },
            "name": "Titi",
            "race": "elfe"
        }
    ]
}
```

#### *'Search' request*

Search for documents in a given collection.

*Pattern* :  `/{database:[a-z0-9_]+}/{collection:[a-z0-9_]+}/_search`  

##### *Examples*

```javascript
/** POST /forest/characters/_search **/

/* Request body */
{
    "filter": {
        "race": "troll"
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
            "race": "troll"
        }
    ]
}
```

At some point, you may need advanced filters.  
Remember that the API is using MongoDB, therefore you are able to use any special operators provided by MongoDB.

##### *Examples*

```javascript
/**  Find documents with an "age" field greater than 18  **/

/* Request body */
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

/* Request body */
{
    "filter": {
        "category": {
            "$exists": false
        }
    }
}
```

Feel free to use the [MongoDB Documentation](https://docs.mongodb.com/manual/reference/) for any help.

### GET Request

Fetch all the documents for a given resource

* *Pattern* : `/{database:[a-z0-9_]+}[/{collection:[a-z0-9_]+}[/{id:[a-z0-9]+}]]`
* The request body IS NOT used

##### *Examples*

```javascript
/**  GET /forest  **/

/* Response body */
{
    "count": 5
    "collections": [
        {
            "name": "characters",
            "count": 3,
            "data": [
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Toto",
                    "race": "troll"
                },
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Tata",
                    "race": "elfe"
                },
                {
                    "_id": {
                        "oid": // Already auto-generated unique id
                    },
                    "name": "Titi",
                    "race": "elfe"
                }
            ]
        },
        {
            "name": "trees",
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
            "_id": {
                "oid": // Already auto-generated unique id
            },
            "name": "apple",
        },
        {
            "_id": {
                "oid": // Already auto-generated unique id
            },
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

### PUT Request

PUT requests are used to replace a document or a collection of documents.  
Therefore, the API expects the client to send the whole representation of the resource.

Note that the response for a PUT request has no body.

*Pattern* : `/{database:[a-z0-9_]+}/{collection[a-z0-9_]+}/{id:[a-z0-9]+}`

#### *Examples*

```javascript
/** PUT /forest/animals/s0m3un1qu31d  **/

/* Request body */
{
    "data": {
        "name": "Long",
        "race": "tiger"
    }
}
```

If the id doesn't exist, no action will be done and you'll get an error.


```javascript
/** PUT /forest/animals  **/

/* Request body */
{
    "data": [
        {
            "_id": "s0m3un1qu31d"
            "name": "Long",
            "race": "tiger"
        },
        {
            "_id": "an0th3run1qu31d"
            "name": "Bakuryu",
            "race": "mole"
        },
    ]
}
```

Note : Any document with a non-existing id will be ignored without breaking the queue process.
