# Mapper API
##### This is the Rest API documentation of the mapper application which is a study planner application that mainly focuses on scheduling the study time of the subject.
&nbsp;

------------
### Endpoints
##### Base url www.youdomain.com/api
------------
###  1. Sign Up (Register)
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
        "password": "1234567890",
		"device_name": "MY-DEVICE-NAME"
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
	
##### Note
###### You need to store the token securly for feature requests.
<br>

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

------------

###  2. Sign In (Login)
##### Endpoint
###### /login
##### Method
###### POST

##### Header
accept: application/json

##### Body
    {
        "email": "test@email.com",
        "password": "1234567890",
		"device_name": "MY-DEVICE-NAME"
    }

##### Response
	{
		"status": true,
		"token": "17|G26hX2NQWFJCWnb5Fl3GA0p8FIkBpq8zddGL8MYB",
		"user": {
			"id": 8,
			"email": "Collin.Conroy1@gmail.com",
			"first_name": "Saman",
			"last_name": "Perera",
			"email_verified_at": null,
			"created_at": "2022-08-30T18:27:14.000000Z",
			"updated_at": "2022-08-30T18:27:14.000000Z"
		}
	}
	
##### Note
###### You need to store the value securly for feature requests.
<br>

##### Errors
	{
		"message": "The given data was invalid.",
		"errors": {
			"email": [
				"Your email and password do not match."
			]
		}
	}

------------

###  3. Forgot Password
##### Forgot password functionality consist of three steps.

##### Step 1 
###### User will send the email.
##### Step 2
###### User will send the email + OTP.
##### Step 3 
###### User will send the email + OTP + new password.

###  Step 1
##### Endpoint
###### /forgot-password/step-1
##### Method
###### POST

##### Header
accept: application/json

##### Body
    {
        "email": "test@email.com"
    }

##### Response
	{
		"success": true
	}
	
##### Note
######  For security reasons, even if the given user is wrong in step 1, it will send a success message.
<br>

##### Errors
None


###  Step 2
##### Endpoint
###### /forgot-password/step-2
##### Method
###### POST

##### Header
accept: application/json

##### Body
    {
        "email": "test@email.com",
		"otp": "10000"
    }

##### Response
	{
		"success": true
	}
	

<br>

##### Errors
	{
		"message": "The given data was invalid.",
		"errors": {
			"otp": "Invalid OTP, Please try again"
		}
	}

##### Note
######  For security reasons, even if the given email is wrong, it will return the invalid OTP error message.


###  Step 3
##### Endpoint
###### /forgot-password/step-3
##### Method
###### POST

##### Header
accept: application/json

##### Body
    {
        "email": "test@email.com",
		"otp": "10000",
		"password": "1234567890"
    }

##### Response
	{
		"success": true
	}
	

<br>

##### Errors
	{
		"message": "The given data was invalid.",
		"errors": {
			"email": [
				"The selected email is invalid."
			]
		}
	}
