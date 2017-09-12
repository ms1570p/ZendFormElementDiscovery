# ZendFormElementDiscovery

## Description

This library automatically creates form inputs depends on database schema and field name. It automatically assign some validators and filters, for example StringLength or Required depends on database schema.

##Installation

Add to your composer.json:

    "require": {
        ...
        "ms1570p/zend-form-element-discovery": "dev-master"
    }

## Working with ZendFormElementDiscovery

In your form simply add this trait:
```php
use ms1570p\ZendFormElementDiscovery\ZendFormElementDiscoveryTrait;
```
and now you can use the main method called:
```php
addElementDiscovery()
```

## Example
```php

use Model_DbTable_Users as Users;
 
class Default_Form_Register extends Twitter_Bootstrap3_Form_Vertical
{
    use ms1570p\ZendFormElementDiscovery\ZendFormElementDiscoveryTrait;
 
    public function init()
    {
        $this->addElementDiscovery(Users::model(), 'firstname');
        $this->addElementDiscovery(Users::model(), 'lastname');
        $this->addElementDiscovery(Users::model(), 'email', [
            'validators' => [
                ['Db_NoRecordExists', false, [
                    'table' => 'users',
                    'field' => 'email',
                ]]
            ],
        ]);
        $this->addElementDiscovery(Users::model(), 'password');
    }
}
```