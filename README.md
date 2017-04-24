Cassandra client library for Laravel. Integrate Cassandra into your Laravel app. Inspired by [duoshuo/php-cassandra](https://github.com/duoshuo/php-cassandra). Enjoy it!

Table of contents
-----------------
* [Features](#features)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Fetch Data](#fetch-data)
* [Query Asynchronously](#query-asynchronously)
* [Using preparation and data binding](#using-preparation-and-data-binding)
* [Using Batch](#using-batch)
* [Supported datatypes](#supported-datatypes)
* [Using nested datatypes](#using-nested-datatypes)
* [Inspired by](#inspired-by)

Features
--------

* Using Protocol v3 (Cassandra 2.1)
* Support ssl/tls with stream transport layer
* Support asynchronous and synchronous request
* Support for logged, unlogged and counter batches
* The ability to specify the consistency, "serial consistency" and all flags defined in the protocol
* Support Query preparation and execute
* Support all data types conversion and binding, including collection types, tuple and UDT
* Support conditional update/insert
* 5 fetch methods (fetchAll, fetchRow, fetchPairs, fetchCol, fetchOne)
* Two transport layers - socket and stream.
* Using exceptions to report errors
* 800% performance improvement(async mode) than other php cassandra client libraries

Installation
------------

PHP 5.4+ is required.

**Installation using composer:**

```
composer require masoudjahromi/laravel-cassandra "dev-master"
```

OR

**Append dependency into composer.json**

```
	...
	"require": {
		...
		"masoudjahromi/laravel-cassandra": "dev-master"
	}
	...
```

Then run following command:

```
composer update
```

## Basic Usage

```php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Masoudjahromi\LaravelCassandra\Connection;
use Masoudjahromi\LaravelCassandra\Exception;

class UserController extends Controller
{
    public function getData()
    {
        $nodes = [
            '127.0.0.1:9042' // simple way, hostname only
        ];

        // Create a connection.
        $connection = new Connection($nodes, 'my_keyspace');

        //Try to connect Cassandra
        try
        {
            $connect = $connection->connect();
        }
        catch (Exception $e)
        {
        // Handle Exception
        }

        // Run query synchronously.
        try
        {
            $response = $connection->querySync('SELECT * FROM "users"');
            $rows = $response->fetchAll();
        }
        catch (Exception $e)
        {
        // Handle Exception
        }
    }
}


```

## Fetch Data

```php
// Return a SplFixedArray containing all of the result set.
$rows = $response->fetchAll();		// SplFixedArray

// Return a SplFixedArray containing a specified index column from the result set.
$col = $response->fetchCol();		// SplFixedArray

// Return a assoc array with key-value pairs, the key is the first column, the value is the second column. 
$col = $response->fetchPairs();		// assoc array

// Return the first row of the result set.
$row = $response->fetchRow();		// ArrayObject

// Return the first column of the first row of the result set.
$value = $response->fetchOne();		// mixed
```

## Query Asynchronously

```php
// Return a statement immediately
try
{
	$statement1 = $connection->queryAsync($cql1);
	$statement2 = $connection->queryAsync($cql2);

	// Wait until received the response, can be reversed order
	$response2 = $statement2->getResponse();
	$response1 = $statement1->getResponse();


	$rows1 = $response1->fetchAll();
	$rows2 = $response2->fetchAll();
}
catch (Masoudjahromi\Cassandra\Exception $e)
{
}
```

## Using preparation and data binding

```php
$preparedData = $connection->prepare('SELECT * FROM "users" WHERE "id" = :id');

$strictValues = Masoudjahromi\Cassandra\Request::strictTypeValues(
	[
		'id' => 'c5420d81-499e-4c9c-ac0c-fa6ba3ebc2bc',
	],
	$preparedData['metadata']['columns']
);

$response = $connection->executeSync(
	$preparedData['id'],
	$strictValues,
	Masoudjahromi\Cassandra\Request::CONSISTENCY_QUORUM,
	[
		'page_size' => 100,
		'names_for_values' => true,
		'skip_metadata' => true,
	]
);

$response->setMetadata($preparedData['result_metadata']);
$rows = $response->fetchAll();
```

## Using Batch

```php
$batchRequest = new Masoudjahromi\Cassandra\Request\Batch();

// Append a prepared query
$preparedData = $connection->prepare('UPDATE "students" SET "age" = :age WHERE "id" = :id');
$values = [
	'age' => 21,
	'id' => 'c5419d81-499e-4c9c-ac0c-fa6ba3ebc2bc',
];
$batchRequest->appendQueryId($preparedData['id'], Masoudjahromi\Cassandra\Request::strictTypeValues($values, $preparedData['metadata']['columns']));

// Append a query string
$batchRequest->appendQuery(
	'INSERT INTO "students" ("id", "name", "age") VALUES (:id, :name, :age)',
	[
		'id' => new Masoudjahromi\Cassandra\Type\Uuid('c5420d81-499e-4c9c-ac0c-fa6ba3ebc2bc'),
		'name' => new Masoudjahromi\Cassandra\Type\Varchar('Mark'),
		'age' => 20,
	]
);

$response = $connection->syncRequest($batchRequest);
$rows = $response->fetchAll();
```

## Supported datatypes

All types are supported.

```php
//  Ascii
    new Masoudjahromi\Cassandra\Type\Ascii('string');

//  Bigint
    new Masoudjahromi\Cassandra\Type\Bigint(10000000000);

//  Blob
    new Masoudjahromi\Cassandra\Type\Blob('string');

//  Boolean
    new Masoudjahromi\Cassandra\Type\Boolean(true);

//  Counter
    new Masoudjahromi\Cassandra\Type\Counter(1000);

//  Decimal
    new Masoudjahromi\Cassandra\Type\Decimal('0.0123');

//  Double
    new Masoudjahromi\Cassandra\Type\Double(2.718281828459);

//  Float
    new Masoudjahromi\Cassandra\Type\PhpFloat(2.718);

//  Inet
    new Masoudjahromi\Cassandra\Type\Inet('127.0.0.1');

//  Int
    new Masoudjahromi\Cassandra\Type\PhpInt(1);

//  CollectionList
    new Masoudjahromi\Cassandra\Type\CollectionList([1, 1, 1], [Cassandra\Type\Base::INT]);

//  CollectionMap
    new Masoudjahromi\Cassandra\Type\CollectionMap(['a' => 1, 'b' => 2], [Cassandra\Type\Base::ASCII, Masoudjahromi\Cassandra\Type\Base::INT]);

//  CollectionSet
    new Masoudjahromi\Cassandra\Type\CollectionSet([1, 2, 3], [Cassandra\Type\Base::INT]);

//  Timestamp (unit: millisecond)
    new Masoudjahromi\Cassandra\Type\Timestamp((int) (microtime(true) * 1000));
    new Masoudjahromi\Cassandra\Type\Timestamp(1409830696263);

//  Uuid
    new Masoudjahromi\Cassandra\Type\Uuid('62c36092-82a1-3a00-93d1-46196ee77204');

//  Timeuuid
    new Masoudjahromi\Cassandra\Type\Timeuuid('2dc65ebe-300b-11e4-a23b-ab416c39d509');

//  Varchar
    new Masoudjahromi\Cassandra\Type\Varchar('string');

//  Varint
    new Masoudjahromi\Cassandra\Type\Varint(10000000000);

//  Custom
    new Masoudjahromi\Cassandra\Type\Custom('string', 'var_name');

//  Tuple
    new Masoudjahromi\Cassandra\Type\Tuple([1, '2'], [Masoudjahromi\Cassandra\Type\Base::INT, Masoudjahromi\Cassandra\Type\Base::VARCHAR]);

//  UDT
    new Masoudjahromi\Cassandra\Type\UDT(['intField' => 1, 'textField' => '2'], ['intField' => Masoudjahromi\Cassandra\Type\Base::INT, 'textField' => Masoudjahromi\Cassandra\Type\Base::VARCHAR]); 	// in the order defined by the type
```

## Using nested datatypes

```php
// CollectionSet<UDT>, where UDT contains: Int, Text, Boolean, CollectionList<Text>, CollectionList<UDT>
new Masoudjahromi\Cassandra\Type\CollectionSet([
	[
		'id' => 1,
		'name' => 'string',
		'active' => true,
		'friends' => ['string1', 'string2', 'string3'],
		'drinks' => [['qty' => 5, 'brand' => 'Pepsi'], ['qty' => 3, 'brand' => 'Coke']]
	],[
		'id' => 2,
		'name' => 'string',
		'active' => false,
		'friends' => ['string4', 'string5', 'string6'],
		'drinks' => []
	]
], [
	[
	'type' => Masoudjahromi\Cassandra\Type\Base::UDT,
	'definition' => [
		'id' => Masoudjahromi\Cassandra\Type\Base::INT,
		'name' => Masoudjahromi\Cassandra\Type\Base::VARCHAR,
		'active' => Masoudjahromi\Cassandra\Type\Base::BOOLEAN,
		'friends' => [
			'type' => Masoudjahromi\Cassandra\Type\Base::COLLECTION_LIST,
			'value' => Masoudjahromi\Cassandra\Type\Base::VARCHAR
		],
		'drinks' => [
			'type' => Masoudjahromi\Cassandra\Type\Base::COLLECTION_LIST,
			'value' => [
				'type' => Masoudjahromi\Cassandra\Type\Base::UDT,
				'typeMap' => [
					'qty' => Masoudjahromi\Cassandra\Type\Base::INT,
					'brand' => Masoudjahromi\Cassandra\Type\Base::VARCHAR
				]
			]
		]
	]
]
]);
```

## Inspired by
* [duoshuo/php-cassandra](https://github.com/duoshuo/php-cassandra)
