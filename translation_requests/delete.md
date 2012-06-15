Read a Translation Request
==========================

DELETE /translation_requests/{translation\_request\_id}
-------------------------------------------------------

Delete the requested translation request.  This is a hard delete, so upon deletion,  you will need to request a new translation token.

**Resource URL**

/translation_requests/{translation\_request\_id}.format (.json or .xml)

**Parameters**

* translation\_request\_id (**required**) - The id for the translation request your deleting.

**Example Request**

_JSON_

DELETE /translation_requests/1.json

```json
{"vts":
	{	"status":"success",
		"message":"Your translation request has been deleted.",
		"translation_requests":[]
	}
}
```

_XML_

DELETE /translation_requests/1.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>success</status>
	<message>Your translation request has been deleted.</message>
	<translation_requests/>
</vts>
```