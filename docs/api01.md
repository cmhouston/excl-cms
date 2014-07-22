# ExCL API (version 1.0) #

We have designed the API to provide two levels of data/granularity: museum level and component level.

## General API Format ##

The API returns data in JSON format. Each response will have the following structure:

	{
		"status": "ok", // one of "ok" or "error"
		"error": "...", // This may or may not exist, depending on whether there is an error or not. This gives the error message
		"data": {
			. . .
		}
	}

The `data` object will in turn have a different sub-object for each level. The object will be called either `museums`, `museum`, `components`, or `component`.

If the API finds no data for a particular field, it will return `false`.

## Museum Level ##

The museum level API allows you to query for all museums or a specific museum. To get all museums, hit:

	/wp-json/v01/excl/museum

To get a specific museum's information, hit:

	/wp-json/v01/excl/museum/<id>

For example, to get the museum's information with id of 13, go to `/wp-json/v01/excl/museum/13`.

Here is an example of the data the API returns with a single museum (`/wp-json/v01/excl/museum/1`):

	{
		"status": "ok",
		"data": {
			"museum": {
				"id": 1,
				"name": "Children's Museum of Houston",
				"description": "This is the museum description.",
				"prices": "Adult - $9",
				"map": "http://cmhouston.org/map.jpg",
				"image": "http://cmhouston.org/pic.jpg",
				"website": "http://cmhouston.org",
				"phone": "7878787878",
				"email": "info@cmhouston.org",
				"exhibits": [
					{
						"id": 2,
						"name": "How does it work?",
						"description": "This exhibit shows you how stuff works!!",
						"image": "http://cmhouston.org/pic.jpg",
						"components": [
							{
								"id": 3,
								"name": "Spinning Disc",
								"image": false
							},
							{
								"id": 4,
								"name": "Cups and Balls",
								"image": "http://cmhouston.org/pic.jpg"
							}
						]
					}
				]
			}
		}
	}

## Component Level ##
The component level API allows you to query for all components or a specific component. To get all components, hit:

	/wp-json/v01/excl/component

To get a specific component's information, hit:

	/wp-json/v01/excl/component/<id>

For example, to get the component's information with id of 23, go to `/wp-json/v01/excl/component/23`.

Here is an example of the data the API returns with a single component (`/wp-json/v01/excl/component/23`):

	{
		"status": "ok",
		"data": {
			"component": {
				"id": 23,
				"name": "Spinning Disc",
				"posts": [
					{
						"id": 41,
						"name": "Spin the Disc",
						"thumbnail": "",
						"section": "What do I do?",
						"liking": true,
						"sharing": false,
						"commenting": true,
						"social-media-message": "#SpunTheDisc and it was great! #cmh",
						"like-count": "",
						"comments": [
							{
								"id": "2",
								"body": "This is a comment",
								"date": "2014-06-16 16:20:13"
							}
						],
						"parts": [
							{
								"id": 42,
								"name": "Spin the Disc Video",
								"type": "text", // one of text, image, video, or poll (poll isn't implementd yet though)
								"url": "http://cmhouston.org" // only used for image and video types
								"body" // only used for text types
							}
						]
					}
				]
			}
		}
	}