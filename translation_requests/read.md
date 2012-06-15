Read a Translation Request
==========================

GET /translation_requests/{translation\_request\_id}
----------------------------------------------------

Gets the details about the requested translation request.

**Resource URL**

/translation_requests/{translation\_request\_id}.format (.json or .xml)

**Parameters**

* translation\_request\_id (**required**) - The id for the translation request your looking for.

**Example Request**

_JSON_

GET /translation_requests/1.json

`{"vts":
	{	"status":"success",
		"message":"",
		"translation_requests":[
			{	"id":"1",
				"token":"tr7a01835c0c14a30b9fce330f6",
				"created":"2012-06-14 08:52:07",
				"modified":"2012-06-14 08:52:07",
				"expires_at":"2012-06-15 08:52:07"
			}
		]
	}
}`

_XML_

GET /translation_requests/1.xml

`<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>success</status>
	<message/>
	<translation_requests>
		<id>1</id>
		<token>tr7a01835c0c14a30b9fce330f6</token>
		<created>2012-06-14 08:52:07</created>
		<modified>2012-06-14 08:52:07</modified>
		<expires_at>2012-06-15 08:52:07</expires_at>
	</translation_requests>
</vts>`