<?php

// Read all the rows from the table measure_1
// https://websitebeaver.com/php-pdo-prepared-statements-to-prevent-sql-injection

$dsn = "mysql:host=localhost;dbname=dashboard;charset=utf8mb4";
$options = [
    PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
];
try {
    $pdo = new PDO($dsn, "root", "", $options);

    if(empty($pdo))
    {
        exit("Could not create PDO object");
    }
    
    // Get distinct values for reporterid 
    $stmt = $pdo->prepare("SELECT distinct reporterid FROM measure_1");
    $stmt->execute();
    $reporterid = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$reporterid) exit('No rows');
    //var_export($reporterid);
    $stmt = null;

    // Gather all the rows for a particular reporterid in an associative array.
    $reporteridData = [];
    foreach ($reporterid as $id) {
        // Get unique years for this reporterid
        $stmt = $pdo->prepare("SELECT distinct year FROM measure_1 where reporterid = :id");
        $stmt->execute([':id' => $id['reporterid']]);
        $years = $stmt->fetchAll(PDO::FETCH_NUM);
        $stmt = null;

        foreach ($years as $year) {
            $stmt = $pdo->prepare("SELECT * FROM measure_1 where reporterid = :id and year = :yr");
            $stmt->execute([ ':id' => $id['reporterid'], ':yr' => $year[0]]);
            $reporteridData[$id['reporterid']][$year[0]] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt = null;
        
    }
    
    // print_r($reporteridData['0115']['2017']);
    
    print_r(array_keys($reporteridData));
    
    
    
    
    
} catch (Exception $e) {
    error_log($e->getMessage());
    exit('Something weird happened'); //something a user can understand
}




?>
