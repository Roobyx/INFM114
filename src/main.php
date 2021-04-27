<?php 
	// Include the parser library
	include_once("./vendor/simple_html_dom.php");
	include "./src/mLib.php";

	$isLoading = true;
	$scrapeError = '';
	$statusMessage = 'pending';
	$userHasData = true;
	global $currentSemester;
	$fNumber = $_SESSION["fNumber"];


	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['virgin']) {
		if(!empty($_POST["eStudentPass"])) {
			$post_viewState = '';
			$post_viewStateGenerator = '';
			$post_eventValidation = '';
			$html = '';
			$tempPass = $_POST["eStudentPass"];
	
			// Get initial data to build the POST
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://student.nbu.bg/Default.aspx?ReturnUrl=");
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($curl);
	
			$html = new simple_html_dom();
			$html->load($response);
	
			$post_viewState = $html->find('input[id=__VIEWSTATE]')[0]->value;
			$post_viewStateGenerator = $html->find('input[id=__VIEWSTATEGENERATOR]')[0]->value;
			$post_eventValidation = $html->find('input[id=__EVENTVALIDATION]')[0]->value;
	
			// Build the POST request
			$formParams = array(
				'__VIEWSTATE' => $post_viewState,
				'__VIEWSTATEGENERATOR' => $post_viewStateGenerator,
				'__EVENTVALIDATION' => $post_eventValidation,
				'fn' => $fNumber,
				'password' => $tempPass,
				'btnLogin' => 'Вход'
			);
	
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($formParams));
			curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
			$response = curl_exec($curl);
	
			if ($response) {
				// Get to the schedule page
				curl_setopt($curl, CURLOPT_URL, "https://student.nbu.bg/schedule/IndGrafik.aspx");
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
				$response = curl_exec($curl);
	
				// Bind the new page to the html var
				$html->load($response);
	
				// Get the schedule table rows
				$scheduleTableRows = $html->find('table[id=ctl00_ContentPlaceHolder_GridView1] tr');
				// Current semester name from the select field in schedule page
				$currentSemester = strip_tags($html->find('select[name="ctl00$master_header$ddlSemester"] option[selected="selected"]')[0]);
	
				$course_html = new simple_html_dom();
	
				foreach ($scheduleTableRows as $key => $row) {

					if($key > 1) {
						// Parse row as DOM element
						$row_html = str_get_html($row);
	
						// Load the row DOM element
						$course_html->load($row_html);
	
						// Assign for ease
						$cell = $course_html->find('td');
	
						// Add the new courses to DB 
						$courseSignature = strip_tags($cell[0]);
						$courseName = strip_tags($cell[1]);
						$courseDay = strip_tags($cell[2]);
	
						$courseDay = translateWeekDays($courseDay, 'int');

						$courseWeek = trim(str_replace('&nbsp;', ' ', strip_tags($cell[3])));

						if(strlen($courseWeek) == 0) {
							$courseWeek = null;
						}

						$courseTeacher = strip_tags($cell[6]);
						
						$courseLocation = trim(str_replace('&nbsp;', ' ', strip_tags($cell[7])));
						if(strlen($courseLocation) == 0) {
							$courseLocation = null;
						}

					
						$courseTime = trim(str_replace('&nbsp;', ' ', strip_tags($cell[8])));
						if(strlen($courseLocation)) {
							$courseTime = explode(" - ", strip_tags($cell[8]));

							$tempTime = $courseTime[0];
							$tempDateTime = new DateTime($tempTime);
							$courseTime[0] = $tempDateTime->format("H:i");

							$tempTime = $courseTime[1];
							$tempDateTime = new DateTime($tempTime);
							$courseTime[1] = $tempDateTime->format("H:i");
							
							
						} else {
							$courseTime = [null,null];
						}

	
						$courseNote = strip_tags($cell[9]);


						
						$emptyNote = 0;
	
						// Debug
						// echo "Row: " . $key . "<br>";
						// echo "--------------------------------" . "<br>";
						// echo "courseSignature: " . $courseSignature . "<br>";
						// echo "courseName: " . $courseName . "<br>";
						// echo "courseDay: " . $courseDay . "<br>";
						// echo "courseWeek: " . $courseWeek . "<br>";
						// echo "courseTeacher: " . $courseTeacher . "<br>";
						// echo "courseLocation: " . $courseLocation . "<br>";
						// echo "courseTime1: " . $courseTime[0] . "<br>";
						// echo "courseTime2: " . $courseTime[1] . "<br>";
						// echo "courseNote: " . $courseNote . "<br>";
						// echo "fNumber: " . $fNumber . "<br>";
						// echo "emptyNote: " . $emptyNote . "<br>";
	
						// Query template
						$stmt = $dbLink -> prepare( "INSERT INTO `courses` (`signature`,`name`,`teacherName`) VALUES (?, ?, ?) 
													ON DUPLICATE KEY UPDATE `signature`= VALUES(`signature`), `name` = VALUES(`name`), `teacherName` = VALUES(`teacherName`) ");
	
						// Binding params to query
						$stmt->bind_param("sss", $courseSignature, $courseName, $courseTeacher);
						
						//Executing the statement
						if($stmt->execute()) {
							// echo "1 Worked" . "<br>";
						} else {
							// echo "1 Failed" . "<br>";
						}
						
						// Add the user'scourses to DB
						if($stmt = $dbLink -> prepare("INSERT INTO `student_courses` (`fnumber`, `signature`, `dayOfWeek`, `week`, `timeStart`, `timeEnd`, `location`) VALUES (?,?,?,?,?,?,?) 
													ON DUPLICATE KEY UPDATE `fnumber`= VALUES(`fnumber`), `signature`= VALUES(`signature`), `dayOfWeek` = VALUES(`dayOfWeek`), `week` = VALUES(`week`), `timeStart` = VALUES(`timeStart`), `timeEnd` = VALUES(`timeEnd`), `location` = VALUES(`location`)")) {
							// echo "Prepare 2 success";
						} else {
							// echo "Prepare 2 failed";
						}
	
						// Binding params to query
						$stmt->bind_param("isiisss", $fNumber, $courseSignature, $courseDay, $courseWeek, $courseTime[0], $courseTime[1], $courseLocation);
	
						//Executing the statement
						if($stmt->execute()) {
							updateUserSessionVariables($dbLink, 0, $currentSemester);
						} else {
							// echo "2 Failed" . "<br>";
						}
					}
	
				}
			} else {
				// TODO: Handle if there is nothing returned
				// echo("De nada");
				$scrapeError = "Въведена е грешка парола или технически проблем";
			}

		} else {
			$scrapeError = "Моля въведете парола";
			// echo "Моля въведете парола";
		}
	} else {
		if($_SERVER["REQUEST_METHOD"] == "POST" && !$_SESSION['virgin']) {
			// Reset so he can re-scrape hes eStudent data
			updateUserSessionVariables($dbLink, 1, $currentSemester);
		}
	}

	// Store additional info in session
	function updateUserSessionVariables($dbCon, $inputVirginValue, $semesterValue) {
		// echo "Setting virgin status to: " . $inputVirginValue;
		$fNumber = $_SESSION["fNumber"];
		$newVirginValue = $inputVirginValue;
		$newSemesterValue = $semesterValue;

		// Query template
		$query = $dbCon -> prepare( "UPDATE `users` SET `virgin`= ?, `latestSemester`= ? WHERE fNumber = ?" );

		// Binding params to query
		$query->bind_param("isi", $newVirginValue, $newSemesterValue, $fNumber);

		//Executing the statement
		if($query->execute()) {
		
			// Query template
			$query = $dbCon -> prepare( "SELECT `virgin`, `latestSemester` FROM `users` WHERE fNumber = ?" );

			// Binding params to query
			$query->bind_param("i", $fNumber);

			if($query->execute()) {

				if($_ENV === 'local') {
					$result = $query->get_result();

					if ($result->num_rows) {
						if ($row = $result->fetch_assoc()) {
							$_SESSION['virgin'] = $row['virgin'];
							$_SESSION['latestSemester'] = $row['latestSemester'];
							// echo "Virgin set Worked. New value: " . $row['virgin'] . "<br>";

						} else {
							// echo "4 Failed" . "<br>";
						}
					}
				} else {
					$resVirgin;
					$reslatestSem;

					if ($query->num_rows) {
						$_SESSION['virgin'] = $query->fetchColumn('virgin');
						$_SESSION['latestSemester'] = $query->fetchColumn('latestSemester');
					}
				}
			}
		} else {
			// echo "3 Failed" . "<br>";
		}
	}

	function translateWeekDays($day, $toType) {
		// echo "translating";
		if($toType === 'int') {
			switch ($day) {
				case 'Понеделник':
					return 1;
					break;
				case 'Вторник':
					return 2;
					break;
				case 'Сряда':
					return 3;
					break;
				case 'Четвъртък':
					return 4;
					break;
				case 'Петък':
					return 5;
					break;
				case 'Събота':
					return 6;
					break;
				case 'Неделя':
					return 7;
					break;
				
				default:
					return null;
					break;
			}
		} elseif($toType === 'bg'){
			switch ($day) {
				case 1: 
					return 'Понеделник';
					break;
				case 2: 
					return 'Вторник';
					break;
				case 3: 
					return 'Сряда';
					break;
				case 4: 
					return 'Четвъртък';
					break;
				case 5: 
					return 'Петък';
					break;
				case 6: 
					return 'Събота';
					break;
				case 7: 
				case 0: 
					return 'Неделя';
					break;
				
				default:
					return 8;
					break;
			}
		} elseif($toType === 'IntToEnShort'){
			switch ($day) {
				case 1: 
					return 'MO';
					break;
				case 2: 
					return 'TU';
					break;
				case 3: 
					return 'WE';
					break;
				case 4: 
					return 'TH';
					break;
				case 5: 
					return 'FR';
					break;
				case 6: 
					return 'SA';
					break;
				case 7: 
				case 0: 
					return 'SU';
					break;
				
				default:
					return null;
					break;
			}
		} elseif($toType === 'BgToEnShort'){
			switch ($day) {
				case 'Понеделник':
					return 'MO';
					break;
				case 'Вторник':
					return 'TU';
					break;
				case 'Сряда':
					return 'WE';
					break;
				case 'Четвъртък':
					return 'TH';
					break;
				case 'Петък':
					return 'FR';
					break;
				case 'Събота':
					return 'SA';
					break;
				case 'Неделя':
					return 'SU';
					break;
				default:
					return null;
					break;
			}
		}
	}

	function getCourseName($signature, $dbCon) {
		// echo "Looking for sign: " . $signature . "<br>";

		$query = $dbCon -> prepare( "SELECT name FROM courses WHERE signature = ?" );
		$query->bind_param("s", $signature);

		if($query->execute()) {
			$name = '';

			$signatureReqResult = $query->get_result();
			if($signatureReqResult->num_rows) {
				while($row = $signatureReqResult->fetch_assoc()) {
				// foreach($signatureReqResult as $row) {
					$name = $row['name'];
					// echo "Got name: " . $name . "<br>";
				}
			}
		}

		return $name;
	}

	function setSessionSchedule($dbLink) {
		// Get schedule and build schedule table
		$fNumber = $_SESSION["fNumber"];

		// Query template
		$query = $dbLink -> prepare( "SELECT signature, dayOfWeek, week, timeStart, timeEnd, location FROM student_courses WHERE fnumber = ?" );

		// Binding params to query
		$query->bind_param("i", $fNumber);

		if($query->execute()) {
			if($_ENV === 'local') {
				$result = $query->get_result();

				if ($result->num_rows) {
					// Store current schedule in the session
					$tempSchedule = [];
	
					while($row = $result->fetch_assoc()) {
						$tempName = getCourseName($row['signature'], $dbLink);
						$course = [$row['signature'], $tempName, translateWeekDays($row['dayOfWeek'], 'bg'), $row['week'], $row['timeStart'], $row['timeEnd'], $row['location']];
						array_push($tempSchedule, $course);
					}
					
					$_SESSION['schedule'] = $tempSchedule;

					// echo "schedule should be set: " . isset($_SESSION['schedule']);
				}
			} else {
				if ($query->num_rows) {
					$tempSchedule = [];

					$tempName = getCourseName($query->fetchColumn('signature'), $dbLink);
					$course = [$query->fetchColumn('signature'), 
								$tempName, translateWeekDays($query->fetchColumn('dayOfWeek'), 'bg'), 
								$query->fetchColumn('week'), $query->fetchColumn('timeStart'), 
								$query->fetchColumn('timeEnd'), $query->fetchColumn('location')];

					array_push($tempSchedule, $course);
					$_SESSION['schedule'] = $tempSchedule;

					// echo "schedule should be set: " . isset($_SESSION['schedule']);
				}
			}
		} else {
			// echo "Failed" . "<br>";
		}
	}

	function getSessionSchedule($dbLink, $type) {
		$scheduleArray =  $_SESSION['schedule'];


		$gCalendarIcon = '<?xml version="1.0" encoding="iso-8859-1"?>
		<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
		<path style="fill:#FBBB00;" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456C103.821,274.792,107.225,292.797,113.47,309.408z"/><path style="fill:#518EF8;" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z"/><path style="fill:#28B446;" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"/><path style="fill:#F14336;" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0C318.115,0,375.068,22.126,419.404,58.936z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

		$aCalendarIcon = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		viewBox="0 0 311.265 311.265" style="enable-background:new 0 0 311.265 311.265;" xml:space="preserve"><g><path d="M151.379,82.354c0.487,0.015,0.977,0.022,1.464,0.022c0.001,0,0.001,0,0.002,0c17.285,0,36.041-9.745,47.777-24.823C212.736,42.011,218.24,23.367,215.723,6.4c-0.575-3.875-4.047-6.662-7.943-6.381c-17.035,1.193-36.32,11.551-47.987,25.772c-12.694,15.459-18.51,34.307-15.557,50.418C144.873,79.684,147.848,82.243,151.379,82.354z M171.388,35.309c7.236-8.82,18.949-16.106,29.924-19.028c-0.522,14.924-8.626,27.056-12.523,32.056c-7.576,9.732-19.225,16.735-30.338,18.566C158.672,52.062,168.14,39.265,171.388,35.309z"/><path d="M282.608,226.332c-0.794-1.91-2.343-3.407-4.279-4.137c-30.887-11.646-40.56-45.12-31.807-69.461c4.327-12.073,12.84-21.885,24.618-28.375c1.938-1.068,3.306-2.938,3.737-5.109c0.431-2.171-0.12-4.422-1.503-6.149c-15.654-19.536-37.906-31.199-59.525-31.199c-15.136,0-25.382,3.886-34.422,7.314c-6.659,2.525-12.409,4.706-19.001,4.706c-7.292,0-13.942-2.382-21.644-5.141c-9.003-3.225-19.206-6.88-31.958-6.88c-24.577,0-49.485,14.863-65.013,38.803c-5.746,8.905-9.775,19.905-11.98,32.708c-6.203,36.422,4.307,79.822,28.118,116.101c13.503,20.53,30.519,41.546,54.327,41.749l0.486,0.002c9.917,0,16.589-2.98,23.041-5.862c6.818-3.045,13.258-5.922,24.923-5.98l0.384-0.001c11.445,0,17.681,2.861,24.283,5.89c6.325,2.902,12.866,5.903,22.757,5.903l0.453-0.003c23.332-0.198,41.002-22.305,55.225-43.925c8.742-13.391,12.071-20.235,18.699-35.003C283.373,230.396,283.402,228.242,282.608,226.332z M251.281,259.065c-11.329,17.222-26.433,37.008-42.814,37.148l-0.318,0.001c-6.615,0-10.979-2.003-16.503-4.537c-7.046-3.233-15.815-7.256-30.538-7.256l-0.463,0.001c-14.819,0.074-23.77,4.072-30.961,7.285c-5.701,2.547-10.204,4.558-16.923,4.558l-0.348-0.001c-16.862-0.145-31.267-18.777-41.929-34.987c-21.78-33.184-31.45-72.565-25.869-105.332c1.858-10.789,5.155-19.909,9.79-27.093c12.783-19.708,32.869-31.951,52.419-31.951c10.146,0,18.284,2.915,26.9,6.001c8.262,2.96,16.805,6.02,26.702,6.02c9.341,0,16.956-2.888,24.32-5.681c8.218-3.117,16.717-6.34,29.104-6.34c14.739,0,30.047,7.097,42.211,19.302c-11.002,8.02-19.102,18.756-23.655,31.461c-11.872,33.016,2.986,69.622,33.334,85.316C261.229,242.764,258.024,248.734,251.281,259.065z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

		if($type === 'courses') {
			echo "
				<thead>
					<th> Сигнатура </th>
					<th> Име </th>
					<th> Ден </th>
					<th> Седмица </th>
					<th> Начало </th>
					<th> Край </th>
					<th> Място </th>
					<th> Добави в календар </th>
				</thead>
			"; 
			
			foreach ($scheduleArray as $key => $row) {

				$tStart = "";
				$tEnd = "";
				$calendarGoogleLink = "";
				$calendarAppleLink = "";
				$eventDescription = "НБУ Лекция - Събитието е генерирано чрез www.nbumanager.com";
				$eventName = $row[0] . ": " . $row[1];

				$eventLink = EventLink::createEvent($row[4], $row[5], $row[2], $eventName, $eventDescription, $row[6]);
				$courseGoogleEvent = $eventLink->generateGoogleLink();

				// TODO: Validate
				$courseAppleEvent = $eventLink->generateICSLink();

				if($row[4] && $type === 'courses') {
					$calendarGoogleLink = "<a href='" . $courseGoogleEvent . "' target='_blank' class='table-link'>" . $gCalendarIcon . "</a>";
					// $calendarAppleLink = "<a href='" . $courseAppleEvent . "' target='_blank' class='table-link'>" . $aCalendarIcon . "</a>";
					$tStart = "<div class='time'>" . date_create($row[4])->format("H:i") . "</div>";
					$tEnd = "<div class='time is-wait'>" . date_create($row[5])->format("H:i") . "</div>";

					if(translateWeekDays(date('w'), 'bg') === $row[2]) {
						echo "<tr class='isToday'>";
					} else {
						echo "<tr>";
					}
		
					echo "<td>" . $row[0] . " </td>
							<td> " . $row[1] . " </td>
							<td> " . $row[2] . " </td>
							<td> " . $row[3] . " </td>
							<td> " . $tStart . " </td>
							<td> " . $tEnd  . " </td>
							<td> " . $row[6] . "</td>
							<td>" . $calendarGoogleLink . $calendarAppleLink . "</td>";
					echo "</tr>";
				}
			}
		} else {

			echo "
				<thead>
					<th> Сигнатура </th>
					<th> Име </th>
				</thead>
			"; 
		
			foreach ($scheduleArray as $key => $row) {
				if(!$row[4]) {
					echo "<tr>";
						echo "<td>" . $row[0] . " </td>
							<td> " . $row[1] . " </td>
							<td></td>";
					echo "</tr>";
				}
			}
		}
	}

	function getCurrentCurrentSemesterInfo() {
		// Get initial data to build the POST
		$nbuCurl = curl_init();
		curl_setopt($nbuCurl, CURLOPT_URL, "https://www.nbu.bg/bg/students/kalendar");
		curl_setopt($nbuCurl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($nbuCurl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($nbuCurl);

		$html = new simple_html_dom();
		$html->load($response);

		$calendarTableSemster = $html->find('table');
		$calendarTableSemester1 = $html->find('table ')[0]->find('tr', 4)->find('td', 1);
		$calendarTableSemester2 = $html->find('table')[1]->find('tr', 4)->find('td', 1);
		$semester1Term = strip_tags($html->find('table')[1]->find('tr', 10)->find('td', 1));
		$semester2Term = strip_tags($html->find('table')[1]->find('tr', 10)->find('td', 1));


		$today = new DateTime("now");
		$semester1 = explode(' - ', strip_tags($calendarTableSemester1));
		$semester2 = explode(' - ', strip_tags($calendarTableSemester2));

		$semester1Start = new DateTime($semester1[0]);
		$semester1End = new DateTime($semester1[1]);

		$semester2Start = new DateTime($semester2[0]);
		$semester2End = new DateTime($semester2[1]);


		$currentSemester;
		$currentSemesterWeek;
		$upcomingTerm = '';

		if($today > $semester1Start && $today < $semester1End) {
			$currentSemester = "Семестър 1 - Есенен";
			$currentSemesterWeek = intval(intval($semester1Start->diff($today)->format('%a')) / 7);
			$upcomingTerm = $semester1Term;
		} elseif($today > $semester2Start && $today < $semester2End) {
			$currentSemester = "Семестър 2 - Пролетен";
			$currentSemesterWeek = intval(intval($semester2Start->diff($today)->format('%a')) / 7);
			$upcomingTerm = $semester2Term;
		} else {
			$currentSemester = "Лятна Ваканция";
			$currentSemesterWeek = 'Неприложимо';
		}

		$_SESSION['currentSemester'] = $currentSemester;
		$_SESSION['currentSemesterWeek'] = $currentSemesterWeek;
		$_SESSION['upcomingTerm'] = $upcomingTerm;
	}
?>