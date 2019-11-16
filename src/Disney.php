<?php
/** <git commit test>
  * Class for accessing DOM data representation of the contents of a Disney.xml
  * file
  */
class Disney
{
    /**
      * The object model holding the content of the XML file.
      * @var DOMDocument
      */
    protected $doc;

    /**
      * An XPath object that simplifies the use of XPath for finding nodes.
      * @var DOMXPath
      */
    protected $xpath;

    /**
      * param String $url The URL of the Disney XML file
      */
    public function __construct($url)
    {
        $this->doc = new DOMDocument();
        $this->doc->load($url);
        $this->xpath = new DOMXPath($this->doc);
    }

    /**
      * Creates an array structure listing all actors and the roles they have
      * played in various movies.
      * returns Array The function returns an array of arrays. The keys of they
      *               "outer" associative array are the names of the actors.
      *                The values are numeric arrays where each array lists
      *                key information about the roles that the actor has
      *                played. The elments of the "inner" arrays are string
      *                formatted this way:
      *               'As <role name> in <movie name> (movie year)' - such as:
      *               array(
      *               "Robert Downey Jr." => array(
      *                  "As Tony Stark in Iron Man (2008)",
      *                  "As Tony Stark in Spider-Man: Homecoming (2017)",
      *                  "As Tony Stark in Avengers: Infinity War (2018)",
      *                  "As Tony Stark in Avengers: Endgame (2019)"),
      *               "Terrence Howard" => array(
      *                  "As Rhodey in Iron Man (2008)")
      *               )
      */
    public function getActorStatistics()  //check the Disney.xml file to get context on the comments
    {
        $x = array(); //array initialisation
		
		$Act = $this->xpath->query('Actors/Actor');               //this is a query that fetches or "points" to the actor elements,
                                                                  // do note that this is classed as the DOMNodeList class because the query returns a list of every element that matches the query search
		foreach($Act as $actor) {                                 //this loop goes trough each node of the $Act list of nodes and sets them as "$actor"
               $actorId = $actor->getAttribute('id');             //this gets the attribute 'id' from the current node, this is used below
			 
			   $x[$actorId] = array();                            //this creates a new array inside the $x array, the "box" (marked with the actors id "$actorId" inside $x array now contains an empty array
			   
		       $RoleNodes = $this->xpath->query("Subsidiaries/Subsidiary/Movie/Cast/Role[@actor = '$actorId']");       //this fetches a list of nodes that matches the query, here its finds roles of the actors using $actorId
		        foreach($RoleNodes as $RoleElement){                                                                   //goes trough each role that the actors have played
                    $rolesName = "{$RoleElement->getAttribute('name')}";       //because $RoleNodes returned a list of the Role elements that has actor attribute = $actorId, $rolesName will get the name attribute of Role element
					
					$movieNodeN = $RoleElement->parentNode->parentNode->getElementsByTagName("Name")->item(0); //here we return to the Movie element by using parentNode twice and then get the Name value of Movie element
					$moviesName = $movieNodeN->nodeValue; //because the lists we have used are all DOMnodeList or DOMElement(like $movieNodeN) we have to convert it to string, and nodeValue does just that (returns value of node)
					                                      //Notice: the reason ->item(0) has to be used is because if not, $movieNodeN would be a list, not a single element, and foreach loops aleready do make lists into singles
														  
					$movieNodeY = $RoleElement->parentNode->parentNode->getElementsByTagName("Year")->item(0); //the same as above but instead of "Name" but with "Year" instead
					$movieYear = $movieNodeY->nodeValue;                                                       //$movieYear becomes a int var because thats what nodeValue returns here
					
					$x[$actorId][] = "As $rolesName in $moviesName ($movieYear)";                              //the "boxes" marked with $actorId have their arrays filled with this string contructed with moviename and year
		        }
           }
        //To do:
        // Implement functionality as specified  -- DONE! :D
        print_r($x);
    }

    /**
      * Removes Actor elements from the $doc object for Actors that have not
      * played in any of the movies in $doc - i.e., their id's do not appear
      * in any of the Movie/Cast/Role/@actor attributes in $doc.
      */
    public function removeUnreferencedActors()   //some of the code is ripped from the previous function
    {
		$compName = $this->xpath->query('Actors/Actor');                                                          //query actor
		$domElemsToRemove = array();                                                                              //initialise array
		
		foreach($compName as $Aname){                                                                             //for each of the nodes in $compName
			$Compare = $Aname->getAttribute('id');                                                                //get the actor id
			$RoleNodesCopy = $this->xpath->query("Subsidiaries/Subsidiary/Movie/Cast/Role[@actor = '$Compare']"); //query all roles with the actorid as actor in roles
			$AllRoles = count ($RoleNodesCopy);                                                                   //now count the amount of nodes in $RoleNodesCopy
			if($AllRoles == 0){                                                                                   //if it is 0 (if the actor has no roles)
				 $domElemsToRemove[] = $Aname;                                                                    //the actor object is put into an array
			}
		}
		foreach( $domElemsToRemove as $domElement ){ 
            $domElement->parentNode->removeChild($domElement);                                                    //for each actor role in this array, delete them
        }                                                                                                         //the reason why we cant delete them straight away is because $domElemsToRemove is a list, not single elements
        //To do:
        // Implement functionality as specified -- DONE! :D

    }

    /**
      * Adds a new role to a movie in the $doc object.
      * @param String $subsidiaryId The id of the Disney subsidiary
      * @param String $movieName    The name of the movie of the new role
      * @param Integer $movieYear   The production year of the given movie
      * @param String $roleName     The name of the role to be added
      * @param String $roleActor    The id of the actor playing the role
      * @param String $roleAlias    The role's alias (optional)
      */
    public function addRole($subsidiaryId, $movieName, $movieYear, $roleName,
                            $roleActor, $roleAlias = null)   //these are variables inherited from the test file, we have to use these, but not all of them, to add new role.
    {
		
		$SetNewHere = $this->xpath->query("Subsidiaries/Subsidiary/Movie[Name[contains(text(),'$movieName')]]/Cast")->item(0);    //queiries a movie which has the name inside $moviename
		$NewEl = $this->doc->createElement('Role', '');                                                //creates a new element, you have to use $this->doc as its the object that holds the informastion of teh disney.xml file
		$NewEl->setAttribute("name", "$roleName");                                                     //sets attribute, the first argument is what type it is and the next is what the attribute contains
		$NewEl->setAttribute("actor", "$roleActor");                                                   //sets teh second attribute
		$SetNewHere->appendChild($NewEl);                                                              //append child adds a nide (in this case the node we created) into the node's childnode list
        //To do:                                                                                       //IMPORTANT NOTE: $SetNewHere is actually a DOMNodeList, so we have to put ->item(index) to make it a single element
        // Implement functionality as specified                                                        //because appentChild will not work for DOMNodeList, only for DOMElement.

    }
}
?>
