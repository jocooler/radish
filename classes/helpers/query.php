<?php
class Query {
  //TODO make it so the installer can easily change these. Maybe an ini file like http://php.net/manual/en/class.pdo.php#89019
  private $server;
  private $user;
  private $password;
  private $database;
  private $connection;
  private $statement;

  public $query = '';
  public $parameters = array();
  public $results;

  // PDO->execute - multiple times, have to fetch
  // PDO->query - one time select, returns results

  public function __construct($query, array $parameters = array()) {
    $credentials = parse_ini_file("db.conf");
    $this->server = $credentials['server'];
    $this->user = $credentials['user'];
    $this->password = $credentials['password'];
    $this->database = $credentials['database'];
    
    $this->query = $query;
    $this->connection = $this->connect_db();
    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // TODO might want to remove this for production
    $this->statement = $this->connection->prepare($this->query);

    if (count($parameters) > 0) {
      $this->execute($parameters);
      return $this->results;
    }
  }

  private function connect_db() {
    try {
  	  $connection = new PDO("mysql:dbname={$this->database};host={$this->server}", $this->user, $this->password);
    } catch (PDOException $e) {
      // TODO proper db error handling.
      echo 'Connection failed: ' . $e->getMessage();
    }
  	return $connection;
  }

  public function execute(array $parameters) {
    $this->parameters = $parameters;
    try {
      $this->statement->execute($this->parameters);
      $this->results = $this->statement->fetchAll();
      return $this->results;
    } catch (PDOException $e) {
      // TODO proper db error handling.
      echo 'Failure: ' . $e->getMessage();
    }
  }
}
?>
