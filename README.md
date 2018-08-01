Make sure to have `extension=php_mongodb.dll` in a `php.ini`

Run unit tests with 

```
.\vendor\bin\phpunit --bootstrap vendor\autoload.php tests
```

# GET /
### Get list of bookings
`curl -X GET http://localhost:8000/api.php`
```
[{"_id": "someid", "username": "someuser", "visitReason": "somereason", "startDateTime": "2018-07-31T23:06", "endDateTime": "2018-07-31T23:08"}, {...]
```

# GET /{_id}
### Get a booking by id
`curl -X GET http://localhost:8000/api.php?_id=someid`
```
{"_id": "someid", "username": "someuser", "visitReason": "somereason", "startDateTime": "2018-07-31T23:06", "endDateTime": "2018-07-31T23:08"}
```

# POST /
### Create a new booking and returns information on new booking or errors
```
Accepted fields
- username
- visitReason
- startDateTime
- endDateTime
```
`curl -X POST -F "username=someuser" -F "visitReason=somereason" -F "startDateTime=2018-07-31T23:06" -F "endDateTime=2018-07-31T23:08" http://localhost:8000/api.php`

```
{"_id": "someid", "username": "someuser", "visitReason": "somereason", "startDateTime": "2018-07-31T23:06", "endDateTime": "2018-07-31T23:08"}
```

**example error**
`curl -X POST -F "username=someuser" -F "visitReason=somereason" -F "startDateTime=2018-07-31T23:06" -F "endDateTime=2018-07-30T23:08" http://localhost:8000/api.php`

```
{"endDateTime": "end date time must be in the future"}
```


---
# POST /{_id}
### Update a booking by id, returns updated information or error
```
Accepted fields
- _id
- username
- visitReason
- startDateTime
- endDateTime
```
`curl -X POST -F "_id=some_id" -F "visitReason=some_reason" http://localhost:8000/api.php`
```
{"_id": "some_id", "username": "some_username", "visitReason": "some_reason", "startDateTime": "2018-07-30T02:04", "endDateTime": "2018-07-30T03:04"}
```

**example error**
`curl -X POST -F "_id=some_id" http://localhost:8000/api.php`

```
{"status": 400, "detail": "no fields given to update"}
```


---
# DELETE /{_id}
### Delete a booking by GET param id
`curl -X DELETE http://localhost:8000/api.php?_id=some_id`

```
{"detail": {"message": "document deleted"}}
```