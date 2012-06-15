Errors
======

Here is a list of common errors you might experience in this API.

404 Not Found
-------------

The resource you provide does not exist in the database.  Please verify you passed the id for the resource, and the id is valid.

**Example**

__JSON__

`{"vts":
	{	"status":"error",
		"message":"Invalid resource id provided."
	}
}`

__XML__

`<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>error</status>
	<message>Invalid resource id provided.</message>
</vts>`

405 Method Not Allowed
----------------------

The HTTP method you are passing is invalid.  The methods should be:

* GET - list all or a single resources details
* POST - create a new resource
* PUT - edit an existing resource
* DELETE - delete an existing resource

**Example**

__JSON__

`{"vts":
	{	"status":"error",
		"message":"Invalid http method provided."
	}
}`

__XML__

`<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>error</status>
	<message>Invalid http method provided.</message>
</vts>`

500 Internal Server Error
-------------------------

Our server had difficulty processing your request.  Please try your request again.

**Example**

__JSON__

`{"vts":
	{	"status":"error",
		"message":"Sorry,  we have experienced a internal server error."
	}
}`

__XML__

`<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>error</status>
	<message>Sorry,  we have experienced a internal server error.</message>
</vts>`