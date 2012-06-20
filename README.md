API Documentation
=================

The Video Translator Service helps in the translation of video files.  Each video is divided into clips that can be translated.  These clips are sped up or slowed down to match the translation length, then merged into a single file.  To find out more,  please check out [this blog post](http://www.dsmedia.org/blog/how-open-bible-stories-works).

HTTP-based
----------

This API use the power of HTTP request protocols.  These are the supported HTTP request protocols:

* GET - retrieving information about a resource.
* POST - create a new resource.
* PUT - modify a current resource.  You will need to do a POST with an attribute "_method=PUT", to use this method.
* DELETE - delete a current resource.  You will need to do a POST with an attribute "_method=DELETE", to use this method.

REST
----

This API conforms to the design principles of Representational State Transfer (REST). Simply change the format extension a request to get results in the format of your choice.  We currently accept the JSON and XML format.

Form Based Parameters
---------------------

Currently this API only accepts form based data submission.  All parameters should have a content type of `application/x-www-form-urlencoded` or `multipart/form-data` .




* [API Response Codes](https://github.com/MissionalDigerati/video_translator_service/blob/gh-pages/response_codes.md)
* **Translation Request**
	* [Create](https://github.com/MissionalDigerati/video_translator_service/blob/gh-pages/translation_requests/create.md)
	* [Delete](https://github.com/MissionalDigerati/video_translator_service/blob/gh-pages/translation_requests/delete.md)
	* [Read Details](https://github.com/MissionalDigerati/video_translator_service/blob/gh-pages/translation_requests/read.md)