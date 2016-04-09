Radish - the RESTful Api DrIven Sales Helper
========================
Radish seeks to be an open source, full-featured Point of Sale system and inventory tracker. This is the API side of the project.

Status:
* /product is done except for search/GET and OPTIONS.
* /transactions is done

Deck:
* Source class
* Discount Endpoints, including product groups.
* User class

By decoupling the database/API layer from the front end, we create a system designed to extend and integrate and "play nice" with other products. The aim of this project is to offer a variety of UIs and consumers for the API to make it easy to integrate with websites and existing tools.

Key Features:
* granular permissions at the user and group level
* security for every request
* designed to manage multiple stores and parts of the supply chain
* designed to integrate with anything that can take programmatic input
* designed to manage thousands of products, users, and locations
* modular design to keep functioning even if connectivity is limited
* RESTful and built for use with tools like AJAX and cURL.
* free and open source

Requirements:
* PHP 5.5+
* MySQL or compatible DB
* currently testing on Apache, but hopefully server agnostic. Once complete, reports and pull requests to support other servers welcome.

What follows is my rough planning outline of what the API will look like.

Sample request and response:
`GET https://example.com/api/product/sku/?2040&signature=123&timestamp=1368909000`

`{"2040":{"category":"Infant","discountType":null,"discount":null,"manufacturer":"Rich Frog","name":"Squeak Easy - Puppy","qoh":null,"retail":6.99,"sku":"2040","taxable":false,"upc":"683981052437","wholesale":6.99}}`

```
/transaction (discounts can be created here)
  OPTIONS
  /{ID};{ID}
    GET transaction details
  /{TYPE};{TYPE}?start&end
    POST a new transaction
    GET a list of transactions of certain types

  Transaction should return links to: products and customer

/user (clerks, applications)
  POST a new user, requests username, password returns passphrase and user id
  OPTIONS
  /{ID}
    GET a user's details
    PUT updates to a user
    DELETE a user
  /{group}
    GET users in a group
    POST a new group
    PUT updates to an existing group
    DELETE a group
  /search?name
    GET a list of users by matching

/customer (discounts can be created here)
  POST a new customer
  OPTIONS
    /{ID};{/ID}
      GET customer details
      PUT updates to customer details
      DELETE a customer
    /{group}
      GET customers in a group
      POST a new group
      PUT updates to an existing group
      DELETE a group
    /search?name
      GET a list of customers by matching

  Customer should return links to transactions

/product (discounts can be created here)
  POST a new product
  OPTIONS
  /upc/{UPC};{UPC}
  /sku/{sku};{sku}
  GET - gets data about product or products
  PUT - create/modify a product
  DELETE - removes a product
  /search
    ?name
    ?manufacturer
    ?sku
    ?upc
    GET data about products most closely matching the query

  Product should return links to manufacturer

/source (manufacturers and reps)
  GET
  OPTIONS
  /manufacturer/{NAME};{NAME}
  /rep/{NAME};{NAME}
    GET
    PUT
    DELETE
  /search?name
    GET

/report
  OPTIONS
  GET (links to reports)
  ?q
    GET
  /name
    GET
    PUT
    DELETE
```
  Report should return all links

All ends have a ?show and ?hide to adjust returned fields. ?show is exclusive, ?hide is inclusive.

Rather than having an htaccess, just have index.phps and require the method file (GET, POST, etc.)
Default to "not allowed"

GET - retrieve data
PUT - modify/create data. Idempotent (multiple requests don't make a change) (4x0 === 4x0x0)
DELETE - remove data
HEAD - get metadata without fetching the body
OPTIONS - what methods am I allowed to do?
POST - allow the server to put the data on the server where it wants to. Not idempotent (4x2 !== 4x2x2)

Response times: 0.1 seconds offsite, 0.06 onsite.

Authentication:
Realms allow GET on each endpoint.
Use self-signed https on each domain, basic authentication.
Browser clients will have to visit the site in the browser and confirm the exception before using a client.
Each user needs a public + secret password. Easily differentiated
ID the user - public + secret
Prevent tampering - public + non-transmitted secret (diceware word phrase)

https://github.com/digitalbazaar/forge

http://stackoverflow.com/questions/5507234/how-to-use-basic-auth-with-jquery-and-ajax
