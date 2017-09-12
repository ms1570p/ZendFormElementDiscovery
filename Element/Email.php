<?php

namespace App\ZendFormElementDiscovery\Element;

/**
 * Class Email
 *
 * @package App\ZendFormElementDiscovery\Element
 */
class Email extends Base
{
    /**
     * @var string
     */
    protected $elementClass = \Twitter_Bootstrap3_Form_Element_Email::class;

    /**
     * @var array
     */
    protected $validators = [
        ['EmailAddress']
    ];
}
