# Mapper API
##### This is the Rest API documentation of the mapper application which is a study planner application that mainly focuses on scheduling the study time of the subject.
&nbsp;

------------
### Endpoints
##### Base url www.youdomain.com/api
------------
###  1. Sign Up
##### Endpoint
###### /register
##### Method
###### POST

##### Header
accept: application/json

##### Body
    {
        "email": "test@email.com",
        "first_name":  "Saman",
        "last_name": "Perera",
        "password": "1234567890"
    }

##### Response
	{
		"status": true,
		"token": "5|ngE9WCqle2fGsgkcJNgrrFXzv1dAbm1EOpEHlQba",
		"user": {
			"email": "test@email.com",
			"first_name": "Saman",
			"last_name": "Perera",
			"updated_at": "2022-08-30T18:03:24.000000Z",
			"created_at": "2022-08-30T18:03:24.000000Z",
			"id": 6
		}
	}

##### Errors
	{
		"message": "The given data was invalid.",
		"errors": {
			"email": [
				"The email field is required."
			],
			"first_name": [
				"The first name field is required."
			],
			"last_name": [
				"The last name field is required."
			],
			"password": [
				"The password field is required."
			]
		}
	}
