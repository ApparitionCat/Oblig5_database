<?php
require_once("src/Disney.php");

class DisneyTest
{
    // tests
    /**
      * Tests that actors and roles are properly loaded into the array structure
      */
    public function testActorStatistics()
    {
        $disney = new Disney('data/Disney.xml');
        $list = $disney->getActorStatistics();
//        print_r($list);
        $this->assertEquals(91, count($list));
        $this->assertTrue(key_exists('James Earl Jones', $list));
        $this->assertEquals(3, count($list['James Earl Jones']));
        $this->assertTrue(in_array('As Mufasa in The Lion King (2019)', $list['James Earl Jones']));
        // To be tested:
        // Actor Rizwan Ahmed should be in the list
        // Actor Rizwan Ahmed has not played in any of the movies in the document
    }

    /**
      * Tests that actors listed in the Disney file but which are not playing
      * any role in the cast of any of the Movies listed in the file.
      */
    public function testRemoveUnreferencedActors()
    {
        $disney = new Disney('data/Disney.xml');
        $disney->removeUnreferencedActors();
        $list = $disney->getActorStatistics();
//        print_r($list);
        // To be tested:
        // There should now be only 89 actors in the list
        // Actor Rizwan Ahmed should not be in the list
        // Actor Erik Thomas von Detten should not be in the list
    }

    /**
      * Tests that a new role is successfully added to the list
      * of roles in the movie's cast.
      */
    public function testAddRole()
    {
        // Test data for adding a new role
        $subsidiaryId = 'MarvelStudios';
        $movieName = 'Avengers: Infinity War';
        $movieYear = '2018';
        $roleName = 'Loki';
        $roleActor = 'TomHiddleston';
        $actorName = 'Tom Hiddleston';

        $disney = new Disney('data/Disney.xml');
        $disney->addRole($subsidiaryId, $movieName, $movieYear, $roleName,
                               $roleActor);
        $list = $disney->getActorStatistics();
//        print_r($list);
        // To be tested:
        // The array of roles that Tom Hiddleston has played should now show
        // that he played as Loki in Avengers: Infinity War (2018)
    }

    public function runTests() {
        try {
            $this->testActorStatistics();
            print("Actor statistics test passed\n");
        } catch (Exception $e)
        {
            print("Actor statistics test failed: {$e->getMessage()} on line {$e->getLine()}\n");
        }
        try {
            $this->testRemoveUnreferencedActors();
            print("Remove unreferenced actors test passed.\n");
        } catch (Exception $e)
        {
            print("Remove unreferenced actors test failed: {$e->getMessage()} on line {$e->getTrace()[0]['line']}\n");
        }
        try {
            $this->testAddRole();
            print("Add role test passed\n");
        } catch (Exception $e)
        {
            print("Add actor test failed: {$e->getMessage()} on line {$e->getTrace()[0]['line']}\n");
        }
    }

    public function assertTrue($val)
    {
        if (!$val) {
            throw new Exception("Failed asserting that $val is True\n");
        }
    }

    public function assertFalse($val)
    {
        if ($val) {
            throw new Exception("Failed asserting that $val is False\n");
        }
    }

    public function assertEquals($expected, $actual)
    {
        if ($expected != $actual) {
            throw new Exception("Failed asserting that $actual is equal to $expected\n");
        }
    }
}

$test = new DisneyTest();
$test->runTests();
