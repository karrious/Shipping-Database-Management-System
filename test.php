  <?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_keke7";			// change "cwl" to your own CWL
$config["dbpassword"] = "a20484721";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>CPSC 304 PHP/Oracle Demonstration</title>
</head>

<body>
	<h2>Reset</h2>
	<p>Press reset to clear all messages at the bottom</p>

	<form method="POST" action="test.php">
		<!-- "action" specifies the file or page that will receive the form data for processing. As with this example, it can be this same file. -->
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

	<h2>Insert Query: register a new customer</h2>
	<form method="POST" action="test.php">
		<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
		Name: <input type="text" name="insName"> <br /><br />
		Email: <input type="text" name="insEmail"> <br /><br />
        Address: <input type="text" name="insAddress"> <br /><br />
        Postal Code: <input type="text" name="insPostalCode"> <br /><br />
        Phone Number: <input type="text" name="insNum"> <br /><br />

		<input type="submit" value="Insert" name="insertSubmit"></p>
	</form>

	<hr />

	<h2>Deletion Query: Remove an existing order</h2>
    <form method="POST" action="test.php">
        <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
        Order ID: <input type="text" name="delOrder"> <br /><br />
        <input type="submit" value="Delete" name="deleteSubmit"></p>
    </form>

	<hr />

	<h2>Update Customer Information</h2>
	<form method="POST" action="test.php">
		<input type="hidden" id="updateCustomerRequest" name="updateCustomerRequest">
		Email: <input type="text" name="Email"> <br /><br />
		New Phone Number: <input type="text" name="PhoneNumber"> <br /><br />

		<input type="submit" value="Update Customer Information" name="updateCustomerInformation"></p>
	</form>

	<hr />

    <h2>Selection Query: find orders placed by selected customer</h2>
    <form method="GET" action="test.php">
        <input type="hidden" id="displaySelectionRequest" name="displaySelectionRequest">
        Order ID: <input type="text" name="insOID"> <br /><br />
        Email: <input type="text" name="inse"> <br /><br />
        <select name="andOr">
          <option value="Select">Select</option>
          <option value="and">and</option>
          <option value="or">or</option>
        </select>
        <input type="submit" value="Submit" name="displaySelectionTuples"></p>
    </form>

    <hr />

    <h2>Projection Query</h2>
    <form method="GET" action="test.php">
        <input type="hidden" id="displayProjectionRequest" name="displayProjectionRequest">
         Column: <input type="text" name="proAtt"> <br /><br />
         Table: <input type="text" name="proTab"> <br /><br />
         <input type="submit" value="Projection" name="projectionSubmit"></p>
    </form>

    <hr />


	<h2>Join Customer Information</h2>
    <form method="GET" action="test.php">
        <input type="hidden" id="joinCustomerRequest" name="joinCustomerRequest">
        Email: <input type="text" name="Email"> <br /><br />

        <input type="submit" value="Join Customer Information" name="joinCustomerInformation"></p>
    </form>

    <hr />

    <h2>Aggregation with GROUP BY query: Find the average claim amount by claim type</h2>
    <form method="GET" action="test.php">
        <select name="insurance">
            <option value="damaged">Damaged</option>
            <option value="lost">Lost</option>
            <option value="delayed">Delayed</option>
        </select>
        <input type="hidden" id="avgRequest" name="avgRequest">
        <input type="submit" name="avgTuples"></p>
    </form>

    <hr />

    <h2>Aggregation with HAVING query: Find the average/max/min parcel price by status greater than 40 (the minimum price)</h2>
    <form method="GET" action="test.php">
        <select name="having">
            <option value="average">average</option>
            <option value="max">max</option>
            <option value="min">min</option>
        </select>
        <input type="hidden" id="havingRequest" name="havingRequest">
        <input type="submit" name="havingSubmit"></p>
    </form>

	<hr />

    <h2>Nested Aggregation with GROUP BY: Find the least claimed insurance type</h2>
    	<form method="GET" action="test.php">
    		<input type="hidden" id="findLeastClaimTypeRequest" name="findLeastClaimTypeRequest">

    		<input type="submit" value="Find Least Claimed Type" name="findLeastClaimTypeSubmit"></p>
    	</form>

    <hr />

	<h2>Division: Find the shipping companies that assigns carriers to all shipping routes</h2>
	<form method="GET" action="test.php">
		<input type="hidden" id="divisionTupleRequest" name="divisionTupleRequest">
		<input type="submit" value="Find Companies" name="divisionTuples"></p>
	</form>


	<?php
	// The following code will be parsed as PHP

	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{ //takes a plain (no bound variables) SQL command and executes it
		//echo "<br>running ".$cmdstr."<br>";
		global $db_conn, $success;

		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			echo htmlentities($e['message']);
			$success = False;
		}

		// using @ to supress error message so when insert happens, we only have 1 error message
		$r = @oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
            if ($e['code'] == "02391") {
                 echo "<br>Please try again later.<br>";
                 echo "<br>";
            } elseif ($e['code'] == "00936") {
            } else {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
             	echo htmlentities($e['message']);
            }
            $success = False;
        }

		return $statement;
	}

	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				//echo $val;
				//echo "<br>".$bind."<br>";
				oci_bind_by_name($statement, $bind, $val);
				unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
			}

            // using @ to supress error message so when insert happens, we only have 1 error message
			$r = @oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				if ($e['code'] == "02291") {
                    echo "<br>Customer failed to be added to the system: please enter a valid postal code!<br>";
                } elseif ($e['code'] == "01400") {
				    echo "<br>Customer failed to be added to the system: please enter a value into all blanks!<br>";
				} elseif ($e['code'] == "00001") {
                    echo "<br>This customer already exist in the system, please use the update function if you want to change phone number.<br>";
                } elseif ($e['code'] == "02391") {
                     echo "<br>Please try again later.<br>";
                } elseif ($e['code'] == "00936") {
                } else {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    echo htmlentities($e['message']);
                }
				$success = False;
			}
		}
	}

	function connectToDB()
	{
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	function handleUpdateRequest()
	{
		global $db_conn;

		$Email = $_POST['Email'];
		$PhoneNumber = $_POST['PhoneNumber'];
		$result0 = executePlainSQL("SELECT * FROM Customer WHERE Email = '$Email'");
		if (@oci_fetch_row($result0)[0] != 0) {
		    executePlainSQL("UPDATE Customer SET PhoneNumber='$PhoneNumber' WHERE Email='$Email'");
		    echo "<br>Customer information successfully updated, see table below:<br>";
		} else {
		    echo "<br>Customer does not exist, please enter a valid Email.<br>";
		}

		oci_commit($db_conn);
		$result = executePlainSQL("SELECT * FROM Customer");

		echo "<style>th { padding-right: 15px; }</style>";
        echo "<br>Customer Information:<br>";
        echo "<table>";
        echo "<tr><th>Email</th><th>CustomerName</th><th>PhoneNumber</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["EMAIL"] . "</td><td>" . $row["CUSTOMERNAME"] . "</td><td>" . $row["PHONENUMBER"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
	}

	function handleJoinRequest()
	{
	    global $db_conn;

	    $Email = $_GET['Email'];
	    $result = executePlainSQL("SELECT PostalCodeInfo.City FROM Customer, PostalCodeInfo
        	        WHERE Customer.Email = '$Email' AND Customer.PostalCode = PostalCodeInfo.PostalCode");
	    if ($first = OCI_Fetch_Array($result, OCI_ASSOC)) {
            //prints results from a select statement
            echo "<style>th { padding-right: 15px; }</style>";
            echo "<br>Customer information successfully found, see city information below:<br>";
            echo "<table>";
            echo "<tr><th>City</th></tr>";
            echo "<tr><td>" . $first["CITY"] . "</td></tr>"; //or just use "echo $row[0]"

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["CITY"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        } else {
            echo "<br>Customer does not exist, please enter a valid Customer Email. <br>";
        }
	}

	function handleNestedGroupByRequest()
	{
	    global $db_conn;
	    $result = executePlainSQL("SELECT ClaimType, count(*) FROM Insurance GROUP BY ClaimType
                                   HAVING count(*) <= all (SELECT count(*) FROM Insurance GROUP BY Insurance.ClaimType)");
        echo "<style>th { padding-right: 15px; }</style>";
        echo "<br>Customer The least claimed insurance type is:<br>";
        echo "<table>";
        echo "<tr><th>Claim Type</th><th>Number Of The Claim Type</th></tr>";
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
	}

	function handleResetRequest()
	{
		global $db_conn;
		oci_commit($db_conn);
	}

	function handleInsertRequest()
	{
		global $db_conn;

		$resultBefore = executePlainSQL("SELECT COUNT(*) FROM Customer");
        $rowBefore = oci_fetch_array($resultBefore, OCI_BOTH);
        $countBefore = $rowBefore[0];

		//Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['insEmail'],
			":bind2" => $_POST['insName'],
			":bind3" => $_POST['insAddress'],
			":bind4" => $_POST['insNum'],
			":bind5" => $_POST['insPostalCode']
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("insert into customer values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
		oci_commit($db_conn);

		$resultAfter = executePlainSQL("SELECT COUNT(*) FROM Customer");
        $rowAfter = oci_fetch_array($resultAfter, OCI_BOTH);
        $countAfter = $rowAfter[0];

        if ($countAfter > $countBefore) {
            echo "<br>New customer successfully added.<br>";
        }

        $result = executePlainSQL("SELECT * FROM customer");
        echo "<style>th { padding-right: 15px; }</style>";
        echo "<br>Customer Information:<br>";
        echo "<table>";
        echo "<tr><th>Email</th><th>CustomerName</th><th>Address</th><th>PhoneNumber</th><th>PostalCode</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["EMAIL"] . "</td><td>" . $row["CUSTOMERNAME"] . "</td><td>" . $row["ADDRESS"] . "</td><td>" . $row["PHONENUMBER"] . "</td><td>" . $row["POSTALCODE"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
	}

	function handleDeleteRequest() {
        global $db_conn;

        @executePlainSQL("DELETE FROM LandParcel WHERE OrderID = '" . $_POST['delOrder'] . "'");
        oci_commit($db_conn);
        @executePlainSQL("DELETE FROM SeaParcel WHERE OrderID = '" . $_POST['delOrder'] . "'");
        oci_commit($db_conn);
        @executePlainSQL("DELETE FROM AirParcel WHERE OrderID = '" . $_POST['delOrder'] . "'");
        oci_commit($db_conn);

        $resultBefore = executePlainSQL("SELECT COUNT(*) FROM Orders");
        $rowBefore = oci_fetch_array($resultBefore, OCI_BOTH);
        $countBefore = $rowBefore[0];

        executePlainSQL("DELETE FROM Orders WHERE OrderID = '" . $_POST['delOrder'] . "'");
        oci_commit($db_conn);

        $resultAfter = executePlainSQL("SELECT COUNT(*) FROM Orders");
        $rowAfter = oci_fetch_array($resultAfter, OCI_BOTH);
        $countAfter = $rowAfter[0];

        if ($countBefore > $countAfter) {
            echo "<br>Order successfully deleted.<br>";
        } else {
            echo "<br>No order was deleted, please enter a valid order ID and try again!<br>";
        }

        $result = executePlainSQL("SELECT * FROM Orders");
        echo "<style>th { padding-right: 15px; }</style>";
        echo "<br>Orders placed by customers:<br>";
        echo "<table>";
        echo "<tr><th>OrderID</th><th>CustomerEmail</th><th>PolicyNumber</th><th>PostalCode</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["ORDERID"] . "</td><td>" . $row["CUSTOMEREMAIL"] . "</td><td>" . $row["POLICYNUMBER"] . "</td><td>" . $row["POSTALCODE"] . "</td></tr>";
        }

        echo "</table>";
    }


	function handleDisplaySelectionRequest()
    {
        global $db_conn;

        if ($_GET['andOr'] == 'and') {
            $result = executePlainSQL("SELECT * FROM orders WHERE CustomerEmail = '" . $_GET['inse'] . "' AND OrderID = '" . $_GET['insOID'] . "'");
        } elseif ($_GET['andOr'] == 'or') {
            $result = executePlainSQL("SELECT * FROM orders WHERE CustomerEmail = '" . $_GET['inse'] . "' OR OrderID = '" . $_GET['insOID'] . "'");
        } elseif ($_GET['andOr'] == 'Select') {
            $result = executePlainSQL("SELECT * FROM orders");
        }

        if ($first = OCI_Fetch_Array($result, OCI_ASSOC)) {
            //prints results from a select statement
            echo "<style>th { padding-right: 15px; }</style>";
            echo "<br>Find orders placed by selected customer:<br>";
            echo "<table>";
            echo "<tr><th>Order ID</th><th>Customer Email</th><th>Policy Number</th><th>Postal Code</th></tr>";
            echo "<tr><td>" . $first["ORDERID"] . "</td><td>" . $first["CUSTOMEREMAIL"] . "</td><td>" . $first["POLICYNUMBER"] . "</td><td>" . $first["POSTALCODE"] . "</td></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<tr><td>" . $row["ORDERID"] . "</td><td>" . $row["CUSTOMEREMAIL"] . "</td><td>" . $row["POLICYNUMBER"] . "</td><td>" . $row["POSTALCODE"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        } else {
            echo "<br>No result matching the inputted values, please double check and try again! <br>";
        }
    }

	function handleDivisionRequest()
        {
            global $db_conn;

            $result = executePlainSQL("SELECT sc.CompanyName FROM ShippingCompany sc WHERE NOT EXISTS
                (SELECT sr.StartCity, sr.EndCity FROM ShippingRoute sr WHERE NOT EXISTS
                    (SELECT a.CompanyName FROM Assign a
                        WHERE a.CompanyName = sc.CompanyName AND a.StartCity = sr.StartCity AND a.EndCity = sr.EndCity))");

            if ($first = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<style>th { padding-right: 15px; }</style>";
                echo "<br>The shipping companies that assigns carriers to all shipping routes are: <br>";
                echo "<table>";
                echo "<tr><th>Company Name</th></tr>";
                echo "<tr><td>" . $first["COMPANYNAME"] . "</td></tr>";
                echo "<tr><td>" . $first["COMPANYNAME"] . "</td></tr>";

                while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                    echo "<tr><td>" . $row["COMPANYNAME"] . "</td></tr>";
                }

            echo "</table>";
            } else {
                echo "<br>No shipping company has assigned carriers to all shipping routes unfortunately.<br>";
            }
        }

	function handleHavingRequest() {
	    global $db_conn;

        if ($_GET['having'] == 'average') {
            $result = executePlainSQL("SELECT Status, AVG(Price) AS AvgPrice FROM AirParcel GROUP BY Status HAVING AVG(Price) > 40");
        } elseif ($_GET['having'] == 'min') {
            $result = executePlainSQL("SELECT Status, MIN(Price) AS MinPrice FROM AirParcel GROUP BY Status HAVING MIN(Price) > 40");
        } elseif ($_GET['having'] == 'max') {
            $result = executePlainSQL("SELECT Status, MAX(Price) AS MaxPrice FROM AirParcel GROUP BY Status HAVING MAX(Price) > 40");
        }

        //prints results from a select statement
        echo "The " . $_GET['having'] . " parcel price grouped by status are: ";
        echo "<style>th { padding-right: 15px; }</style>";
        echo "<table>";
        echo "<tr><th>Status</th><th>Price</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
	}

	function handleProjectionRequest() {
        global $db_conn;

        $attributes = strtoupper($_GET['proAtt']);
        $table = strtoupper($_GET['proTab']);

        $query = "SELECT $attributes FROM $table";

        $result = @executePlainSQL($query);

        echo "<table>";
        echo "<tr>";

        $attrArray = explode(",", $attributes);
        foreach ($attrArray as $attr) {
            echo "<th>" . $attr . "</th>";
        }

        echo "</tr>";

        if ($first = @oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo "<style>th { padding-right: 15px; }</style>";
            echo "<tr><td>" . $first[$attr] . "</td></tr>"; //or just use "echo $row[0]"

            while ($row = @oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo "<tr>";
                foreach ($attrArray as $attr) {
                    echo "<td>" . $row[$attr] . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<br>Input values invalid, please double check!<br>";
        }
    }

	function handleAvgRequest()
	{
		global $db_conn;

		if ($_GET['insurance'] == 'damaged') {
            $result = executePlainSQL("SELECT avg(ClaimAmount) FROM Insurance WHERE ClaimType = 'Damaged'");
        } elseif ($_GET['insurance'] == 'lost') {
            $result = executePlainSQL("SELECT avg(ClaimAmount) FROM Insurance WHERE ClaimType = 'Lost'");
        } elseif ($_GET['insurance'] == 'delayed') {
            $result = executePlainSQL("SELECT avg(ClaimAmount) FROM Insurance WHERE ClaimType = 'Delayed'");
        }

		if (($row = oci_fetch_row($result)) != false) {
			echo "<br> The average claimed amount of " . $_GET['insurance'] . " packages are: " . $row[0] . "<br>";
		}
	}

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('updateCustomerRequest', $_POST)) {
				handleUpdateRequest();
			} else if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			} else if (array_key_exists('deleteQueryRequest', $_POST)){
			    handleDeleteRequest();
		    }
			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('avgTuples', $_GET)) {
				handleAvgRequest();
			} elseif (array_key_exists('displaySelectionTuples', $_GET)) {
				handleDisplaySelectionRequest();
            } elseif (array_key_exists('joinCustomerInformation', $_GET)) {
                handleJoinRequest();
            } elseif (array_key_exists('findLeastClaimTypeSubmit', $_GET)) {
                handleNestedGroupByRequest();
            } elseif (array_key_exists('projectionSubmit', $_GET)) {
                handleProjectionRequest();
            } elseif (array_key_exists('havingSubmit', $_GET)) {
                handleHavingRequest();
            } elseif (array_key_exists('divisionTuples', $_GET)) {
				handleDivisionRequest();
			}
			disconnectFromDB();
		}
	}

	if (isset($_POST['reset']) || isset($_POST['updateCustomerInformation']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['avgRequest']) || isset($_GET['displaySelectionRequest']) || isset($_GET['joinCustomerRequest'])
	|| isset($_GET['findLeastClaimTypeRequest']) || isset($_GET['divisionTupleRequest'])  || isset($_GET['displayProjectionRequest'])
	 || isset($_GET['havingRequest']) || isset($_GET['findLeastClaimTypeRequest'])) {
		handleGETRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
