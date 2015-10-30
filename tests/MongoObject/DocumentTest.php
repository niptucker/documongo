<?php

use \documongo\MongoObject\DocumentType;
use \documongo\MongoObject\Document;
use \MongoClient;

class DocumentTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerForCreate
     */
    public function testCreateDelete($mn, $prefix, $typeObject, $uuid)
    {

        $testDocument = Document::create($mn, $prefix, $typeObject, $uuid);

        $this->assertEquals($testDocument->uuid, $uuid);

        $ok = $testDocument->save();

        $this->assertEquals($ok, true);

        $this->assertEquals($testDocument->uuid, $uuid);


        $testDocument2 = Document::findByUuid($mn, $prefix, $uuid);

        $this->assertEquals($testDocument2->uuid, $uuid);

        $this->assertEquals($testDocument, $testDocument2);

        $deleted = $testDocument2->delete();

        $this->assertEquals($deleted, true);
    }

    public function providerForCreate()
    {
        $mn = new MongoClient();
        $prefix = "temp_test_";

        $typeObject = DocumentType::findByType($mn, $prefix, "test_type");

        $uuid = "test-uuid";

        return array(array($mn, $prefix, $typeObject, $uuid));
    }

}

