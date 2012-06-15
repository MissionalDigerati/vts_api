Create a Translation Request
============================

POST /translation_requests
--------------------------

Creates a new translation request that expires in 1 day.  Once you create a translation request,  you will be given a token that can be passed to other methods of this API.  The token is a unique identifier for your current translation that your preforming.

**Resource URL**

/translation_requests.format (.json or .xml)

**Parameters**

**Example Request**

_JSON_

POST /translation_requests.json

`{"vts":
	{	"status":"success",
		"message":"Your translation request has been created.",
		"translation_requests":[
			{	"id":"50",
				"token":"tr0e64e2cf095a611e8ba33b417",
				"created":"2012-06-14 13:35:40",
				"modified":"2012-06-14 13:35:40",
				"expires_at":"2012-06-15 13:35:40"
			}
		]
	}
}`

_XML_

POST /translation_requests.xml

`<?xml version="1.0" encoding="UTF-8"?>
<vts>
	<status>success</status>
	<message>Your translation request has been created.</message>
	<translation_requests>
		<id>51</id>
		<token>tr8b4e8415d80de2286ec934d57</token>
		<created>2012-06-14 13:37:01</created>
		<modified>2012-06-14 13:37:01</modified>
		<expires_at>2012-06-15 13:37:01</expires_at>
	</translation_requests>
</vts>`