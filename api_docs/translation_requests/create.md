Create a Translation Request
============================

POST /translation_requests
--------------------------

Creates a new translation request that expires in 1 day.  Once you create a translation request,  you will be given a token that can be passed to other methods of this API.  The token is a unique identifier for your current translation that your preforming.

**Resource URL**

/translation_requests.format (.json or .xml)

**Parameters**

**Example Request**

POST /translation_requests.json

`{"vts":
		{	"status":"success",
			"message":"Your translation request has been created.",
			"translation_requests":[
			{"modified":"2012-06-14 08:18:30","created":"2012-06-14 08:18:30","token":"tra38508e1872a9a0f8d8ce7fbf","expires_at":"2012-06-15 8:18:30","id":"39"}
			]
		}
}`