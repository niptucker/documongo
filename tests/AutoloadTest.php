<?php

namespace Documongo\Tests;

class AutoloadTest extends \PHPUnit_Framework_TestCase {

    public function testDocumongoMongoObject() {
        $this->assertTrue(class_exists('\documongo\MongoObject'));
    }
    public function testDocumongoMongoObjectDocument() {
        $this->assertTrue(class_exists('\documongo\MongoObject\Document'));
    }
    public function testDocumongoMongoObjectDocumentType() {
        $this->assertTrue(class_exists('\documongo\MongoObject\DocumentType'));
    }
    public function testDocumongoMongoObjectRule() {
        $this->assertTrue(class_exists('\documongo\MongoObject\Rule'));
    }
    public function testDocumongoPermission() {
        $this->assertTrue(trait_exists('\documongo\permission'));
    }

}