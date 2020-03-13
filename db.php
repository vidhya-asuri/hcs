<?php

// Read all the rows from the table measure_1
// https://websitebeaver.com/php-pdo-prepared-statements-to-prevent-sql-injection


// Get the county ID from POST data
// $countyID = $_POST['countyID'];

// $dataPerCounty = getDataByCounty('0115');


// $monthlyData = getMonthlyData('0115', '1');
$data = getData('0115', '1');

$monthlyData = $data[0];
$quarterlyData = $data[1];
$yearlyData = $data[2];

file_put_contents('county.json', json_encode($data)); 
echo json_encode($data);

/**
 * Returns an assoc array with countyID and month-year-string (in the format 'Jan-2017') as keys.
 * Input arguments are county ID, measure ID.
 *
 */
function getData($countyID, $measureID){

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

        // The output of this function. It will be an assoc array with countyID, month and year as keys
        $reporteridData = [];

        $tableSuffix = str_replace(".", "_", $measureID); // replace '.' with underscore.
        $tableName = "measure_" . $tableSuffix;

        // Get all data for a given county and measure
        $stmt = $pdo->prepare("SELECT numerator, denominator, month, quarter, year, issuppressed FROM $tableName where reporterid = :id");
        $stmt->execute([':id' => $countyID ]);
        $resultRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        $data[0] = getMonthlyData($resultRows);
        $data[1] = getQuarterlyData($resultRows);
        $data[2] = getYearlyData($resultRows);
        
        return $data;
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit('Something weird happened'); //something a user can understand
    }

}

function getMonthlyData($rows)
{
//    print_r($rows);
    
    
    $monthlyData = [];
    $i = 0;
    foreach ($rows as $row)
    {
        $monthString = "";
        if(!empty($row['month']))
        {
            $monthString = getMonthYearString($row['month'], substr($row['year'],-2));
            $monthlyData[$i]['group'] = $monthString;
            $monthlyData[$i]['value'] = $row['numerator'] ;
            $monthlyData[$i]['sup'] = $row['issuppressed'] ;
            
            $i++;
        }
    }
    return $monthlyData;
    
}

function getQuarterlyData($rows)
{
    $quarterlyData = [];
    // Check if there were any results. If there were no result rows, no qaurterly data exists in the table.
    // So, we need to try and calculate the values based on monthly values.
    // First if there was quarterly data available, then gather that.
    $i = 0;
    foreach ($rows as $row)
    {
        $quarterString = "";
        if(!empty($row['quarter']))
        {
            $quarterlyData[$i]['group'] = 'Q'. $row['quarter'] . substr($row['year'], -2);
            $quarterlyData[$i]['value'] = $row['numerator'] ;
            $quarterlyData[$i]['sup'] = $row['issuppressed'] ;
            $i++;
        }
    }
    
    return $quarterlyData;
    
}


function getYearlyData($rows)
{
    $yearlyData = [];
    $i = 0;
    foreach ($rows as $row)
    {
        $quarterString = "";
        if(!empty($row['year']))
        {
            $yearlyData[$i]['group'] = $row['year'];
            $yearlyData[$i]['value'] = $row['numerator'] ;
            $yearlyData[$i]['sup'] = $row['issuppressed'] ;
            $i++;
        }
    }
    return $yearlyData;
}



// Get data for the first quarter.
// Input argument is data for a single year. 
function computeQuarterlyData($yearlyData, $quarter)
{
    $quarterlyData = [];
    switch($quarter)
    {
        case 1: 
            // Filter out all the rows of data corresponding to the first quarter.
            $quarterlyData = array_filter($yearlyData, function($value) {
                // print_r($value);
                return (($value['month'] == 1) || ($value['month'] == 2) || ($value['month'] == 3));
            });
            break;
        case 2:
            // Filter out all the rows of data corresponding to the second quarter.
            $quarterlyData = array_filter($yearlyData, function($value) {
                // print_r($value);
                return (($value['month'] == 4) || ($value['month'] == 5) || ($value['month'] == 6));
            });
            break;
        case 3:
            // Filter out all the rows of data corresponding to the third quarter.
            $quarterlyData = array_filter($yearlyData, function($value) {
                // print_r($value);
                return (($value['month'] == 7) || ($value['month'] == 8) || ($value['month'] == 9));
            });
            break;
                
        case 4:
            // Filter out all the rows of data corresponding to the fourth quarter.
            $quarterlyData = array_filter($yearlyData, function($value) {
                // print_r($value);
                return (($value['month'] == 10) || ($value['month'] == 11) || ($value['month'] == 12));
            });
            break;
                
        default:
            break;
            
    }

    return $quarterlyData;
    
}


/**
 * Returns an assoc array with countyID and month-year-string (in the format 'Jan-2017') as keys.
 * Input arguments are county ID and measure ID 
 * 
 */
// function getMonthlyData($countyID, $measureID){
    
//     $dsn = "mysql:host=localhost;dbname=dashboard;charset=utf8mb4";
//     $options = [
//         PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
//         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
//     ];
//     try {
//         $pdo = new PDO($dsn, "root", "", $options);
        
//         if(empty($pdo))
//         {
//             exit("Could not create PDO object");
//         }
        
//         // The output of this function. It will be an assoc array with countyID, month and year as keys
//         $reporteridData = [];
        
//         $tableSuffix = str_replace(".", "_", $measureID); // replace '.' with underscore.
//         $tableName = "measure_" . $tableSuffix;

        
//         // Get all data for a given county ordered by month
// //        $stmt = $pdo->prepare("SELECT * FROM $tableName where reporterid = :id and month is not null order by year,month");
//         $stmt = $pdo->prepare("SELECT numerator, month as FROM $tableName where reporterid = :id and month is not null order by year,month");
//         $stmt->execute([':id' => $countyID ]);
//         $resultRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
//         $stmt = null;
//         $i = 0;
//         foreach ($resultRows as $row) {
//             // Get month and year string in the format - Jan-2018
//             $monthYearString = getMonthYearString($row['month'], $row['year']);
//             $reporteridData[$i]['group'] = $monthYearString;
//             $reporteridData[$i]['value'] = $row['numerator'];
//             $i++;
//         }

//         return $reporteridData;
        
//         // print_r($reporteridData['0115']['2017']);
//     } catch (Exception $e) {
//         error_log($e->getMessage());
//         exit('Something weird happened'); //something a user can understand
//     }
    
// }

function convertMonthToQuarter($month)
{
    $quarters = ['1' => 'Q1', '2' => 'Q1', '3' => 'Q1', '4' => 'Q2', '5' => 'Q2', '6' => 'Q2', '7' => 'Q3', '8' => 'Q3', '9' => 'Q3', '10' => 'Q4', '11' => 'Q4', '12' => 'Q4'];
    return $quarters[$month] ;
}

function getMonthYearString($month, $year)
{
    $monthNames = ['1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
    return $monthNames[$month] . "-" . $year ;
   
}

// function getDataByCounty($countyID){
    
//     $dsn = "mysql:host=localhost;dbname=dashboard;charset=utf8mb4";
//     $options = [
//         PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
//         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
//     ];
//     try {
//         $pdo = new PDO($dsn, "root", "", $options);
        
//         if(empty($pdo))
//         {
//             exit("Could not create PDO object");
//         }
        
//         // Gather all the rows for a particular reporterid in an associative array.
//         $reporteridData = [];
        
//         // Get unique years for this reporterid (aka $countyID)
//         $stmt = $pdo->prepare("SELECT distinct year FROM measure_1 where reporterid = :id");
//         $stmt->execute([':id' => $countyID ]);
//         $years = $stmt->fetchAll(PDO::FETCH_NUM);
        
//         $stmt = null;
            
//         foreach ($years as $year) {
//             $stmt = $pdo->prepare("SELECT * FROM measure_1 where reporterid = :id and year = :yr");
//             $stmt->execute([ ':id' => $countyID , ':yr' => $year[0]]);
//             $reporteridData[$countyID][$year[0]] = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         }
//         $stmt = null;

//         return $reporteridData;
        
//         // print_r($reporteridData['0115']['2017']);
//     } catch (Exception $e) {
//         error_log($e->getMessage());
//         exit('Something weird happened'); //something a user can understand
//     }
    
    
    
// }



?>
