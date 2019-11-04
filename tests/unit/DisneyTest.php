<?php
require_once("src/Disney.php");

class DisneyTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Disney
     */
    protected $disney;

    protected function _before()
    {
        $this->disney = new Disney('data/Disney.xml');
    }

    protected function _after()
    {
    }

    // tests
    /**
      * Tests that actors and roles are properly loaded into the array structure
      */
    public function testActorStatistics()
    {
        $list = $this->disney->getActorStatistics();
        codecept_debug($list);
        $this->tester->assertEquals(91, count($list));
        $this->tester->assertTrue(key_exists('James Earl Jones', $list));
        $this->tester->assertEquals(3, count($list['James Earl Jones']));
        $this->tester->assertTrue(in_array('As Mufasa in The Lion King (2019)', $list['James Earl Jones']));
		$this->tester->assertTrue(key_exists('Actor Rizwan Ahmed', $list));
		$this->tester->assertEquals(0, count($list['Actor Rizwan Ahmed']));
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
        $this->disney->removeUnreferencedActors();
        $list = $this->disney->getActorStatistics();
        codecept_debug($list);
		$this->tester->assertEquals(89, count($list));
		$this->tester->assertTrue((key_exists('Actor Rizwan Ahmed', $list)) == False);   //i dont know if this actually works but without codecept its better than nothing
		$this->tester->assertTrue((key_exists('Erik Thomas von Detten', $list)) == False);
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

        $this->disney->addRole($subsidiaryId, $movieName, $movieYear, $roleName,
                               $roleActor);
        $list = $this->disney->getActorStatistics();
        codecept_debug($list);
		   $this->tester->assertTrue(in_array('As Loki in Avengers: Infinity War (2018))', $list['Tom Hiddleston']));
        // To be tested:
        // The array of roles that Tom Hiddleston has played should now show   -- dont know this one 
        // that he played as Loki in Avengers: Infinity War (2018)
    }
}
