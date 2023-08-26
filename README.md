# Accredify-laravel-test
This package has been constructed as a complete set of docker containers - contains seperate hosts for php, mysql/mariabd, phpmyadmin and nginx

in order to prepare this package, unsure you have docker installed,  and run the following in the root of this package:
$ docker compose up --build -d

- this will build and launch the complete package

at first  run, you will need to log  into the running php  container, and generate the table/s required for the api to run 100%: 
do the following steps (cmdline fromthe base of this package):
$ docker exec -it docker exec -it lartest-PHP sh
$ cd /code
$ php artisan migrate:refresh

you can access the database tables by doing the following:
 - browse to http://localhost:8183/
 - enter following creds: 
      server: mariadb
      username: lartest
      password: letmein

   select the "lartest" db- there will be an empty "verification_results" table

the endpoint to the user verification api can be foud at: http://localhost:8080/user/verification
 - postman is recommended as a method of testing the api
 
 send JSON payloads to the above endpoint in order to test the verification api
 sample test data and results expected below:

test 1:
complete valid JSON payload:
ie. 
  - recipient.name and recipient.email are provided
  - issuer.name and issue.email are provided
  - issuer.identityProof.key and issuer.identityProof.location are provided, and dns txt records are valid for that domain and txt kerecoord
  - signature.targetHash is is correct according to the sha256 hash agorithm against all the values provided within the verification payload

  payload:
  {
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}

expected result:
{
    "data": {
        "issuer": "Accredify",
        "result": "verified"
    }
}

verification_results table now has an entry with matching id: "63c79bd9303530645d1cca00", and verification_result: "verified"


test 2:
    recipient.name and/or recipient.email missing form  payload:

payload example 1 (missing recipient.name):
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}
payload example 2 (missing recipient.email):
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}

expected result:
{
    "data": {
        "issuer": "Accredify",
        "result": {
            "errors": [
                "invalid_recipient",
                "unverified"
            ]
        }
    }
}
verification_results for  each of theabove tests, the table now has an entry with matching id: "63c79bd9303530645d1cca00", and verification_result: {"errors":["invalid_recipient","unverified"]}

test 3:
    issuer.entityProof and/or issuer.name missing from the payload

payload 1 -  missing issuer.name
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}
payload 2 -  missing issuer.identityProof
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify"
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}
expected  result:
{
    "data": {
        "issuer": "Accredify",
        "result": {
            "errors": [
                "invalid_issuer",
                "unverified"
            ]
        }
    }
}

test 3:
    issuer.entityProof.key is not found in the DNT TXT records for the issuer.identifyProof.location domain lookup

payload 1 - with amended issuer.identityProof.key value (added "*bad*" onto the endof the string)
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller*bad*",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}
payload 2 - with amended issuer.identityProof.location value (added "*bad* onto the endof the string)
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io*bad*"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}

expected results:
{
    "data": {
        "issuer": "Accredify",
        "result": {
            "errors": [
                "invalid_issuer",
                "unverified"
            ]
        }
    }
}

test 4: 
  JSON has a valid signature 
  ie. signature.targethash  does not match the calculated SHA256 hash of allthe combined values witin the json payload

payload - adjusted the signature.targetHash (added "*bad*"onto the end of the hash string)
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e*bad*"
  }
}
expected results: (errors: "unverified")
{
    "data": {
        "issuer": "Accredify",
        "result": {
            "errors": [
                "unverified"
            ]
        }
    }
}
