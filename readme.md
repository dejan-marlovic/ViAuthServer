# Authlib
PHP library for use with the ViAuth API (auth.minoch.com).

**Please note that the API is currently not available for public use.** 

## Server-side installation

#### Step 1 - Fetch files using composer:
composer.json should look like this (ignoring any other dependencies): 
```
{
    "minimum-stability": "dev",
    "repositories":
    [
      {
        "type": "vcs",
        "url": "https://github.com/afpatmin/ViAuthServer"
      }
    ],
    "require": 
    {
        "afpatmin/ViAuthServer": "dev-master"
    }
}
```

#### Step 2 - Setup file access:
The installation contains two files of importance
* auth.php
* index.php

Move [index.php] to a public web folder, and make sure [auth.php] is not accessible 
from the web.

Edit [index.php], at the top of the file, set AUTH_BASE to the relative path where 
[auth.php] is located (ex. *'../../lib/auth.php'*). Set HTTP_ORIGIN to your the 
domain from which you will call the API.

#### Step 3 - Set credentials
Edit [auth.php], at the top of the file, insert your API key under CLIENT_KEY

 