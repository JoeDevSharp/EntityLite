Here is a documentation draft for your `EntityLite` library in PHP, including code examples to demonstrate how to use its components effectively.

---

# EntityLite Documentation

**EntityLite** is a lightweight PHP ORM inspired by Entity Framework, designed to simplify database interactions using an object-oriented approach. This documentation provides an overview of the core classes and how to use them.

## Table of Contents

1. [Installation](#installation)
2. [Database Connection](#database-connection)
3. [DbContext](#dbcontext)
4. [Entity and DbSet](#entity-and-dbset)
5. [BaseRepository](#baserepository)
6. [Example Usage](#example-usage)

## Installation

Make sure to include the `EntityLite` library in your project. If you are using Composer, ensure your `composer.json` is properly configured.

```json
{
  "require": {
    "joedevsharp/entitylite": "^1.0"
  }
}
```

```bash
composer require joedevsharp/entitylite
```

## Database Connection

To connect to your database, you will use the `Database` class. It requires the database credentials to establish a connection.

```php
<?php

use EntityLite\Database;

$database = new Database('localhost', 'username', 'password', 'database_name');
$dbConnection = $database->getConnection();
```

### Constructor Parameters

- `host`: The database host.
- `username`: The username for the database.
- `password`: The password for the database.
- `dbname`: The name of the database.

## DbContext

The `DbContext` class manages database operations and holds references to entity sets.

### Adding Entities

You can add entity sets to the `DbContext` using the `addEntity` method.

```php
<?php

use EntityLite\DbContext;

$dbContext = new DbContext($dbConnection);
$dbContext->addEntity('users', User::class);
```

### Retrieving Entities

You can access the added entities as properties of the `DbContext` instance.

```php
$users = $dbContext->users->findAll(); // Retrieves all users
```

## Entity and DbSet

### Entity Class

The `Entity` class serves as a base class for your entities. It includes a method to convert an object’s properties to an array.

```php
<?php

namespace EntityLite;

abstract class Entity {
    public function toArray(): array {
        return get_object_vars($this);
    }
}
```

### DbSet Class

The `DbSet` class extends the `BaseRepository` class, allowing for CRUD operations on entities.

```php
<?php

use EntityLite\DbSet;

class User extends Entity {
    public $id;
    public $name;
    public $email;
}

// In DbContext
$dbContext->addEntity('users', User::class);
```

## BaseRepository

The `BaseRepository` class provides common methods for database operations, including `findAll`, `findById`, `insert`, `update`, and `delete`.

### Method Examples

#### Find All Records

```php
$users = $dbContext->users->findAll();
```

#### Find Record by ID

```php
$user = $dbContext->users->findById(1);
```

#### Insert Record

```php
$newUser = new User();
$newUser->name = 'John Doe';
$newUser->email = 'john@example.com';

$dbContext->users->insert($newUser);
```

#### Update Record

```php
$existingUser = new User();
$existingUser->name = 'Jane Doe';
$existingUser->email = 'jane@example.com';

$dbContext->users->update(1, $existingUser);
```

#### Delete Record

```php
$dbContext->users->delete(1);
```

## Example Usage

Here's a complete example of how to use the `EntityLite` library to manage a `User` entity.

```php
<?php

require 'vendor/autoload.php';

use EntityLite\Database;
use EntityLite\DbContext;
use EntityLite\User;

$database = new Database('localhost', 'username', 'password', 'database_name');
$dbConnection = $database->getConnection();

$dbContext = new DbContext($dbConnection);
$dbContext->addEntity('users', User::class);

// Insert a new user
$newUser = new User();
$newUser->name = 'John Doe';
$newUser->email = 'john@example.com';
$dbContext->users->insert($newUser);

// Retrieve all users
$users = $dbContext->users->findAll();
foreach ($users as $user) {
    echo $user->name . " - " . $user->email . "\n";
}

// Update a user
$existingUser = new User();
$existingUser->name = 'Jane Doe';
$existingUser->email = 'jane@example.com';
$dbContext->users->update(1, $existingUser);

// Delete a user
$dbContext->users->delete(2);
```

## Conclusion

The `EntityLite` library provides a simple and effective way to manage database operations in PHP using an object-oriented approach. By following the examples provided in this documentation, you can easily implement your own entities and utilize the available methods for CRUD operations.

For further information and advanced usage, feel free to explore the source code and adapt it to your needs.
