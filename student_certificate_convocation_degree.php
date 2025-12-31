<?php
// MODIFICATION DONE BY NICBA ON 22-06-2024
include("includes/session_include.php");
include("includes/enc_conn_include.php");
include("includes/header_validate_code.php");


/*	ACADEMIC YEAR	*/
$academic_year_data = explode("-", $_SESSION["academic_year"]);
$academic_year = trim($academic_year_data[0]);

/*	EXAM ID	*/
$exam_id = validateInputData($_SESSION['exam_id']);


if (!isset($_SESSION["academic_year"]) || !isset($_SESSION['exam_id']) || $_SESSION["academic_year"] == "" || $_SESSION['exam_id'] == "") {
?>
	<script>
		alert('<?php echo "Invalid Access"; ?>');
		window.location = "logout.php";
	</script>
<?php
	exit;
}

$category = validateInputData($_SESSION["category"]);
$encrypt_sessionid = $crypt->encrypt('2468', session_id(), 1);
$pagetoken = pg_escape_string(strip_tags(killChars($_POST["pagetoken"])));

if ($category == 'R' || $category == 'CF' || $category == 'I' || $category == 'CC') {
?>
	<script>
		alert('<?php echo "Invalid Access"; ?>');
		window.location = "logout.php";
	</script>
<?php
	exit;
}



$getMenuRequest =  $_GET['id'];
$salt = md5($_SESSION['salt']);

// UG DEGREE CONVOCATION DEGREE
$ug_consolidated_id = md5('CD1');
$ug_consolidated = md5($salt . $ug_consolidated_id);

// PG DEGREE  CONVOCATION DEGREE
$pg_consolidated_id = md5('CD2');
$pg_consolidated = md5($salt . $pg_consolidated_id);


$headingName = "";
$reportName = "";

if ($getMenuRequest == $ug_consolidated) {
	$inst_graduation_id = '1';
	$headingName = 'UG - DEGREE CERTIFICATES';
	$reportName = 'CD1'; // CD => CONVOCATION DEGREE
	$tblName = "inst.trn_student_profile_ug";
} else if ($getMenuRequest == $pg_consolidated) {
	$inst_graduation_id = '2';
	$headingName = 'PG - DEGREE CERTIFICATES';
	$reportName = 'CD2'; // CD => CONVOCATION DEGREE
	$tblName = "inst.trn_student_profile_pg";
}


/* LOAD INSTITUTION */
$selInstitution = "SELECT * FROM inst.trn_institution_info WHERE institution_code not in ('9999', '0040') ORDER BY institution_code ASC";
$exeInstitution = $db->query($selInstitution);

// POST VALUES FROM THE FORM SEARCH VALUES.
$institution_p = validateInputData($_POST['institution_code']);
$degreeId_p = validateInputData($_POST['degree']);
$course_id = explode('$$$', $_POST['course']);
$courseId_p = validateInputData($course_id[0]);
$studentBatch_p = validateInputData($_POST['student_batch']);

?>

<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Thiruvalluvar University, Vellore, Tamil Nadu, India.</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="images/favicon.ico" />

	<style>
		label {
			font-size: 13px;
		}

		sup {
			color: red !important;
		}
	</style>

	<div id="loaderId" class="mainLoader" style="display:none">
		<div class="preloader" id="preLoaderId">
			<div class="innercircle">
				<h4 style="margin-top: 20px;">TVU</h4>
				NIC
			</div>
		</div>
	</div>
</head>

<body>

	<?php include("header.php"); ?>

	<div class="contact-w3-agileits master" id="contact" style="padding: 0px 0px;">
		<div class="container">
			<h3 class="heading_style" style="font-size: 15px;"><?php echo $headingName; ?><span class="col-md-12" style="text-align:right; "> </span></h3>
		</div>
	</div>

	<?php
	// CD1 => CONVOCATION DEGREE UG
	// CD2 => CONVOCATION DEGREE PG

	if ($reportName == 'CD1' || $reportName == 'CD2') {
	?>
		<div class="container well">
			<div class="contact-w3-agileits master" id="contact" style="padding: 0px 0px;">
				<form id="sel_stud" name="sel_stud" action="#" method="post" enctype="multipart/form-data">
					<input type="hidden" id="pagetoken" name="pagetoken" value="<?php echo $_SESSION["pagetoken"]; ?>">
					<div class="col-md-12">
						<div class="col-md-12">
							<label class="col-md-5 control-label">Institution<sup>*</sup></label>
							<select class="select_box_pad_reg" name="institution_code" id="institution_code">
								<option value="">Select Institution</option>
								<?php

								if ($exeInstitution->rowCount() > 0) {
									while ($resInstitution = $exeInstitution->fetch(PDO::FETCH_ASSOC)) {
										$inst_name = $resInstitution["institution_name"];

										if ($resInstitution["bdu_city"] != '') {
											$bdu_city = $resInstitution["bdu_city"] . ", ";
										}

										if ($resInstitution["bdu_district"] != '') {
											$bdu_district = $resInstitution["bdu_district"] . ", ";
										}

										if ($resInstitution["pincode"] != '') {
											$bdu_pincode = $resInstitution["pincode"] . ".";
										}

										$institution_name = $inst_name . ', ' . $bdu_city . ' ' . $bdu_district . ' ' . $bdu_pincode;

										$selected = "";

										if ($institution_p == $resInstitution['institution_code']) {
											$selected = ' selected="selected"';
										} else if ($institutionCode == $resInstitution['institution_code']) {
											$selected = ' selected="selected"';
										}

								?>
										<option value=<?php echo $resInstitution['institution_code'] ?> <?php echo $selected; ?>>
											<?php echo $resInstitution['institution_code'] . " - " . strtoupper($institution_name); ?> </option>
								<?php
									}
								}
								?>
							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="col-md-4">
							<label class="col-md-5 control-label">Degree<sup>*</sup></label>
							<select class="select_box_pad_reg" name="degree" id="degree">
								<option value="">Select Degree</option>
							</select>
						</div>
						<div class="col-md-4">
							<label class="col-md-5 control-label">Discipline<sup>*</sup></label>
							<select class="select_box_pad_reg" name="course" id="course">
								<option value="">Select Discipline</option>
							</select>
						</div>

						<div class="col-md-4">
							<label class="col-md-5 control-label">Student&nbsp;Batch<sup>*</sup></label>
							<select class="select_box_pad_reg" name="student_batch" id="student_batch">
								<option value="">Select Batch</option>
							</select>
						</div>
					</div>

					<div class="col-md-12" style="text-align:center;" id="button">
						<input type="button" name="getStudDetails" id="getStudDetails" value="Submit" class="btn btn-primary"
							style=" margin:0% 0% 1% 0%; background:#006899" onClick="getStudDetailsList()">
					</div>

				</form>


			</div>
		</div>


		<?php if (isset($institution_p) && isset($degreeId_p) && isset($courseId_p) && isset($studentBatch_p) && $institution_p != "" && $degreeId_p != "" && $courseId_p != "" && $studentBatch_p != "") { ?>


			<div class="contact-w3-agileits master" name="student_profile_details" id="student_profile_details">

				<div class="container">
					<div style="text-align: right;">
						<input class="btn btn-primary" type="button" onclick="getStudentConsolidatedMarksheet()" name="generatepdf" value="DOWNLOAD"
							style="background: #F96;border-color: #F96;color: #000;" />
					</div>

					<?php
					// SELECT COURSE DETAILS
					$getCourseDt = selCourseDetails(killChars($courseId_p));

					$resCourseId = explode("$$$", $getCourseDt);
					$degreeType = $resCourseId[0];
					$degreeName = $resCourseId[1];
					$courseName = $resCourseId[2];
					$courseShortName = $resCourseId[3];

					?>

					<label style="font-size:15px;color:#C00;">
						<?php echo $degreeType . ' - ' . $degreeName . '.- ' . $courseName . ' (' . $courseShortName . ')'; ?>
					</label>

					<table id="" class="table table-striped table-bordered" style="width:100%; margin-top:1%">
						<thead>
							<tr>
								<th style="padding:10px;">#</th>
								<th style="padding:10px;">REGISTER NUMBER</th>
								<th style="padding:10px;">NAME</th>
								<th style="padding:10px;">DOB</th>
								<th style="padding:10px;"><input type="checkbox" class="checkall" data-target=".checkall" value='0' /></th>
							</tr>
						</thead>
						<tbody>
							<?php


							/* $selStudDetails = "select distinct(univ_reg_no) AS student_registration_number, institution_code, 
								(select stud_code FROM inst.view_student_profile_ug_pg b WHERE a.univ_reg_no=b.univ_reg_no AND a.course_id=b.course) AS student_code,
								(select firstname FROM inst.view_student_profile_ug_pg b WHERE a.univ_reg_no=b.univ_reg_no AND a.course_id=b.course) AS student_name,
								(select dob FROM inst.view_student_profile_ug_pg b WHERE a.univ_reg_no=b.univ_reg_no AND a.course_id=b.course) AS dob,   
								course_id, semester FROM student_result.stud_result_0619 a WHERE institution_code= '" . $institution_p . "' 
								AND course_id='" . $courseId_p . "' 
								AND student_batch ='" . $studentBatch_p . "' 
								ORDER BY univ_reg_no asc";

							//echo "P===".$selStudDetails; */


							if ($reportName == 'CD1' || $reportName == 'CD2') {
								$selStudDetails = "SELECT instcode, univ_reg_no, stud_code, firstname, dob, course FROM $tblName
								WHERE instcode= :institution_p 
								AND course=:courseId_p
								AND student_batch =:studentBatch_p
								ORDER BY univ_reg_no asc";
							}

							$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							try {
								$exeStudDetails = $db->prepare($selStudDetails);
								$exeStudDetails->bindParam(':institution_p', killChars($institution_p));
								$exeStudDetails->bindParam(':courseId_p', killChars($courseId_p));
								$exeStudDetails->bindParam(':studentBatch_p', killChars($studentBatch_p));
								$exeStudDetails->execute();
							} catch (PDOException $e) {
								//Do your error handling here
								$message = $e->getMessage();
								echo "POD ERROR 1 <br>" . $message . "<br><br>";
							}


							$arrayNumberNumeric = array(0, 1, 2, 3, 4, 5);
							$arrayNumberRoman = array("0", "I", "II", "III", "IV", "V");

							if ($exeStudDetails->rowCount() > 0) {
								$i = 1;
								while ($row = $exeStudDetails->fetch(PDO::FETCH_ASSOC)) {

									$grade = "";

									$instCode = $row['instcode'];
									$univRegNo = $row['univ_reg_no'];
									$studCode = $row['stud_code'];
									$firstName = $row['firstname'];
									$dob = $row['dob'];
									$course = $row['course'];

									$arrayNumberNumeric = array(0, 1, 2, 3, 4, 5);
									$arrayNumberRoman = array("0", "I", "II", "III", "IV", "V");


									$selGradeDetails = "SELECT part, SUM(subject_credit) AS total_credit, CAST((SUM(CAST(marks as decimal(10,3))) / SUM(subject_credit)) as decimal(10,3)) AS cgpa
									FROM (SELECT  b.part, b.subject_credit, CAST(cast(total_mark as decimal)/10 as decimal(10,3)) AS grade_point, 
									CAST(total_mark as decimal)/10 * subject_credit AS marks
									FROM student_result.stud_result_0619 a
									JOIN inst.mst_overall_unique_subject_code b ON a.course_id::integer=b.course_id::integer AND a.semester::integer=b.semester::integer AND a.subject_unique_code::integer=b.subject_id::integer
									WHERE univ_reg_no ='" . $univRegNo . "'
									AND a.delete_flg IN ('0','E')
									AND a.allow_marksheet = '0'
									AND a.result_display = '0' 
									AND a.result = 'P'
									AND display_status='Y') j
									GROUP BY part ORDER BY part";



									$selGradeDetails = "SELECT part, SUM(subject_credit) AS total_credit, CAST((SUM(CAST(marks as decimal(10,3))) / SUM(subject_credit)) as decimal(10,3)) AS cgpa
									FROM (SELECT  b.part, b.subject_credit, CAST(cast(total_mark as decimal)/10 as decimal(10,3)) AS grade_point, 
									CAST(total_mark as decimal)/10 * subject_credit AS marks
									FROM student_result.stud_result_0619 a
									JOIN inst.mst_overall_unique_subject_code b ON a.course_id::integer=b.course_id::integer AND a.semester::integer=b.semester::integer AND a.subject_unique_code::integer=b.subject_id::integer
									WHERE univ_reg_no =:univRegNo
									AND a.delete_flg IN ('0','E')
									AND a.allow_marksheet = '0'
									AND a.result_display = '0' 
									AND a.result = 'P'
									AND display_status='Y') j
									GROUP BY part ORDER BY part";

									// SET THE PDO ERROR MODE TO EXCEMPTION
									$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									try {
										// Prepare the SQL statement
										$exeGradeDetails = $db->prepare($selGradeDetails);
										// Bind the parameters using bindParam
										$exeGradeDetails->bindParam(':univRegNo', $univRegNo, PDO::PARAM_STR);
										// Execute the query
										$exeGradeDetails->execute();
									} catch (PDOException $e) {
										//Do your error handling here
										$message = $e->getMessage();
										echo "POD ERROR 1 <br>" . $message . "<br><br>";
									}


									if (($exeGradeDetails->rowCount() > 0)) {

										$partdisp1 = "";
										$partdisp2 = "";
										$partdisp3 = "";
										$partdisp4 = "";
										$partdisp5 = "";

										$pdisp = "";

										$partsno = 1;

										$permission = "";

										$resTotal = 0;

										while ($resGradeDetails = $exeGradeDetails->fetch(PDO::FETCH_ASSOC)) {
											$resPart = '';
											$classifiGrade = '';
											$classifiFinalResult = '';

											$totalCredit_r = $resGradeDetails['total_credit'];
											$cgpa_r = $resGradeDetails['cgpa'];

											$keyPartFind = array_search($resGradeDetails['part'], $arrayNumberNumeric);

											$part = $arrayNumberRoman[$keyPartFind];

											/* if ($univRegNo == '20122U10001') {
												echo "A=====" . $part;
												echo "B=====" . $totalCredit_r;
												echo "<br><br>";

												$resTotal +=  $totalCredit_r;
											} */

											$resTotal += $totalCredit_r;

											if ($inst_graduation_id == '1' && $resTotal == '142')
												$permission = '1';
											else if ($inst_graduation_id == '2' && $resTotal == '95')
												$permission = '1';
											else
												$permission = '0';

											$partsno++;
										}
									} else {
										$permission = '0';
									}

									if ($univRegNo == '32322P09019')
										$permission = '1';



									if ($permission == '1') {
							?>
										<tr>
											<td style="padding:10px;"><?php echo $i; ?></td>
											<td style="padding:10px;"><?php echo $univRegNo; ?></td>
											<td style="padding:10px;"><?php echo strtoupper($firstName); ?></td>
											<td style="padding:10px;"><?php echo $dob; ?></td>
											<td style="padding:10px;">
												<input type="checkbox" name="get_report[<?php echo $i; ?>]" id="get_report[<?php echo $i; ?>]"
													class="checkall checkbox_checked" value="<?php echo $row['stud_code']; ?>" <?php echo $checked; ?> />
											</td>
										</tr>
								<?php
										$i++;
									}
								}
								?>
								<input type="hidden" name="total_count" id="total_count" value="<?php echo $i - 1; ?>">
							<?php
							} else {
							?>
								<tr>
									<td colspan="5" style="color:red;text-align:center;padding:10px;">No Records Found..!!</td>
								</tr>

							<?php
							}

							?>
						</tbody>
					</table>

				</div>

			</div>

		<?php } ?>



		<?php if ($inst_graduation_id == '1') { // UG DEGREE PDF PRINTING 
		?>
			<?PHP /* UG CERTIFICATE PRINTING FORM */ ?>
			<form name="student_consolidated_ug" id="student_consolidated_ug" method="post" action="student_provisional_certificate_affiliation_college_ug_pdf.php" target="_blank">
				<input type="hidden" name="institution_code_pc" id="institution_code_pc" value="<?php echo $institution_p; ?>">
				<input type="hidden" name="degree_type_pc" id="degree_type_pc" value="<?php echo $inst_graduation_id; ?>">
				<input type="hidden" name="degree_id_pc" id="degree_id_pc" value="<?php echo $degreeId_p; ?>">
				<input type="hidden" name="course_id_pc" id="course_id_pc" value="<?php echo $courseId_p; ?>">
				<input type="hidden" name="examid_pc" id="examid_pc" value="<?php echo $examIds_p; ?>">
				<input type="hidden" name="student_batch_pc" id="student_batch_pc" value="<?php echo $studentBatch_p; ?>">
				<input type="hidden" name="res_student_text_pc" id="res_student_text_pc">
			</form>
		<?php } else if ($inst_graduation_id == '2') {  // PG DEGREE PDF PRINTING  
		?>

			<?PHP /* PG CERTIFICATE PRINTING FORM */ ?>
			<form name="student_consolidated_pg" id="student_consolidated_pg" method="post" action="student_provisional_certificate_affiliation_college_pg_pdf.php" target="_blank">
				<input type="hidden" name="institution_code_pc_pg" id="institution_code_pc_pg" value="<?php echo $institution_p; ?>">
				<input type="hidden" name="degree_type_pc_pg" id="degree_type_pc_pg" value="<?php echo $inst_graduation_id; ?>">
				<input type="hidden" name="degree_id_pc_pg" id="degree_id_pc_pg" value="<?php echo $degreeId_p; ?>">
				<input type="hidden" name="course_id_pc_pg" id="course_id_pc_pg" value="<?php echo $courseId_p; ?>">
				<input type="hidden" name="examid_pc_pg" id="examid_pc_pg" value="<?php echo $examIds_p; ?>">
				<input type="hidden" name="student_batch_pc_pg" id="student_batch_pc_pg" value="<?php echo $studentBatch_p; ?>">
				<input type="hidden" name="res_student_text_pc_pg" id="res_student_text_pc_pg">
			</form>

		<?php } ?>

	<?php } else {

		include("page_not_found.php");
	} ?>


	<!-- FOOTER -->
	<?php include("footer.php"); ?>
	<!-- FOOTER END	-->


	<script type="text/javascript">
		/*	SCRIPT FOR LOAD DEGREE	*/
		$(document).ready(function() {
			$("#institution_code").change(function() {
				$('#student_profile_details').hide();
				$('#degree').val("");
				$('#course').val("");
				$("#student_batch").val("");
				$("#exam_ids").val("");
				var type = 'getDegreeDetails';
				var mode = 'getInstDegreeDetails';

				var pagetoken = $("#pagetoken").val();
				var degree_type = '<?php echo $inst_graduation_id; ?>';
				var institution = $('#institution_code').val();
				var passdata = '<option value="">Select Discipline</option>';

				$("#loaderId").show();
				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						degree_type: degree_type,
						institution: institution,
						type: type,
						mode: mode
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data) console.log(data); */ ?>
						$('#loaderId').delay(200).fadeOut('slow');
						$('#student_profile_details').hide();
						$("#degree").html(data);
						$("#course").html(passdata);
					}
				});
			});
		});

		/*	SCRIPT FOR LOAD COURSE	*/
		$(document).ready(function() {
			$("#degree").change(function() {
				blockDisplayButton();

				$("#student_batch").val("");
				$("#exam_ids").val("");
				$('#student_profile_details').hide();

				var type = 'getCourseDetails';
				var mode = 'getCourseDetailsMode';
				var pagetoken = $("#pagetoken").val();
				var degreeId = $('#degree').val();
				var institution = $('#institution_code').val();


				$("#loaderId").show();

				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						degreeId: degreeId,
						institution: institution,
						type: type,
						mode: mode
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data) console.log(data); */ ?>
						$('#loaderId').delay(200).fadeOut('slow');
						$("#course").html(data);
					}
				});
			});
		});


		/*	SCRIPT FOR LOAD EXAM ID	*/
		$(document).ready(function() {
			$("#course").change(function() {

				blockDisplayButton(); // FOR DISABLE ETHE REPORT GENERATE BUTTON

				var pagetoken = $("#pagetoken").val();
				var degree_type = '<?php echo $inst_graduation_id; ?>';
				var institution = $('#institution_code').val();
				var degreeId = $('#degree').val();
				var courseId = $('#course').val();
				var type = 'getStudentBatchProfileTbl';
				var mode = 'getStudentBatchProfileTblDisplay';

				$("#loaderId").show();
				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						degree_type: degree_type,
						institution: institution,
						degreeId: degreeId,
						courseId: courseId,
						type: type,
						mode: mode
					},
					cache: false,
					success: function(data) {

						<?php /* alert(data);  console.log(data); */ ?>

						$('#student_profile_details').hide();
						$('#loaderId').delay(200).fadeOut('slow');
						$("#student_batch").html(data);
					}
				});

			});
		});


		/*	SCRIPT FOR LOAD STUDENT BATCH	*/
		$(document).ready(function() {
			$("#student_batch").change(function() {
				$('#student_profile_details').hide();
			});
		});


		<?php //*************************************** GET POSTING VALUES JS FUNCTIONS START ***************************************************// 
		?>

		// SCRIPT FOR LOAD DEGREE => FROM THE POST VALUES 
		<?php if ($institution_p != "" && $inst_graduation_id != "" && $degreeId_p != "") {	?>

			$(document).ready(function() {
				var pagetoken = $("#pagetoken").val();
				var degree_type = '<?php echo $inst_graduation_id ?>';
				var institution = '<?php echo $institution_p ?>';
				var degreeId = '<?php echo $degreeId_p ?>';
				var type = 'getDegreeDetails';
				var mode = 'getInstDegreeDetails';

				$("#loaderId").show();

				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						degree_type: degree_type,
						institution: institution,
						degreeId: degreeId,
						type: type,
						mode: mode
					},
					cache: false,
					success: function(data) {
						$('#loaderId').delay(200).fadeOut('slow');
						$("#degree").html(data);


					}
				});
			});

		<?php } ?>



		// SCRIPT FOR LOAD COURSE => FROM THE POST VALUES 
		<?php if ($institution_p != "" && $inst_graduation_id != "" && $degreeId_p != "" && $courseId_p != "") { ?>

			$(document).ready(function() {
				var pagetoken = $("#pagetoken").val();
				var institution = '<?php echo $institution_p ?>';
				var degreeId = '<?php echo $degreeId_p ?>';
				var courseId = '<?php echo $courseId_p ?>';
				var type = 'getCourseDetails';
				var mode = 'getCourseDetailsMode';

				blockDisplayButton(); // FOR DISABLE ETHE REPORT GENERATE BUTTON

				$("#loaderId").show();

				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						institution: institution,
						degreeId: degreeId,
						courseId: courseId,
						type: type,
						mode: mode
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data); console.log(data); */ ?>

						$('#loaderId').delay(200).fadeOut('slow');
						$("#course").html(data);
					}
				});
			});

		<?php } ?>



		// SCRIPT FOR LOAD STUDENT BATCH ID DETAILS => FROM THE POST VALUES 

		<?php if ($institution_p != "" && $inst_graduation_id != "" && $degreeId_p != "" && $courseId_p != ""  && $studentBatch_p != "") { ?>

			$(document).ready(function() {
				blockDisplayButton(); // FOR DISABLE ETHE REPORT GENERATE BUTTON

				var pagetoken = $("#pagetoken").val();
				var type = 'getStudentBatchProfileTbl';
				var mode = 'getStudentBatchProfileTblDisplay';

				var degree_type = '<?php echo $inst_graduation_id ?>';
				var institution = '<?php echo $institution_p ?>';
				var degreeId = '<?php echo $degreeId_p ?>';
				var courseId = '<?php echo $courseId_p ?>';
				var student_batch = '<?php echo $studentBatch_p ?>';

				$("#loaderId").show();

				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_certificates.php",
					data: {
						pagetoken: pagetoken,
						degree_type: degree_type,
						institution: institution,
						degreeId: degreeId,
						courseId: courseId,
						student_batch: student_batch,
						type: type,
						mode: mode
					},

					cache: false,
					success: function(data) {
						//alert(data);
						$('#loaderId').delay(200).fadeOut('slow');
						$("#student_batch").html(data);
					}
				});
			});

		<?php } ?>


		function blockDisplayButton() {
			var buttonDiv = document.getElementById("button");
			if (buttonDiv.style.display !== "block")
				buttonDiv.style.display = "block";
		}


		function getStudDetailsList() {
			var institutionCode = $('#institution_code').val();
			var degree = $('#degree').val();
			var course = $('#course').val();
			var examIds = $('#exam_ids').val();
			var studentBatch = $('#student_batch').val();

			var reportType = '<?php echo validateInputData(killChars($reportName)); ?>';

			if (institutionCode == "") {
				var msg = "Please Select Institution";
				$('#institution_code').focus();
				$('#institution_code').css('border-color', 'red');
				$('#institution_code').css('box-shadow', '0 0 0.15rem crimson')
				message_error(msg);
				return false;
			} else {
				$('#institution_code').css('box-shadow', '');
				$('#institution_code').css('border-color', '');
			}

			if (degree == "") {
				var msg = "Please Select Degree";
				$('#degree').focus();
				$('#degree').css('border-color', 'red');
				$('#degree').css('box-shadow', '0 0 0.15rem crimson')
				message_error(msg);
				return false;
			} else {
				$('#degree').css('box-shadow', '');
				$('#degree').css('border-color', '');
			}

			if (course == "") {
				var msg = "Please Select Discipline";
				$('#course').focus();
				$('#course').css('border-color', 'red');
				$('#course').css('box-shadow', '0 0 0.15rem crimson')
				message_error(msg);
				return false;
			} else {
				$('#course').css('box-shadow', '');
				$('#course').css('border-color', '');
			}

			if ((reportType == 'SM1') || (reportType == 'SM2')) {

				if (examIds == "") {
					var msg = "Please Select Exam Id";
					$('#exam_ids').focus();
					$('#exam_ids').css('border-color', 'red');
					$('#exam_ids').css('box-shadow', '0 0 0.15rem crimson')
					message_error(msg);
					return false;
				} else {
					$('#exam_ids').css('box-shadow', '');
					$('#exam_ids').css('border-color', '');
				}

				if (studentBatch == "") {
					var msg = "Please Select Student Batch";
					$('#student_batch').focus();
					$('#student_batch').css('border-color', 'red');
					$('#student_batch').css('box-shadow', '0 0 0.15rem crimson')
					message_error(msg);
					return false;
				} else {
					$('#student_batch').css('box-shadow', '');
					$('#student_batch').css('border-color', '');
				}
			}

			blockDisplayButton(); // FOR DISABLE ETHE REPORT GENERATE BUTTON

			document.getElementById("sel_stud").submit();
		}


		// STUDENT CONSOLIDATED MARKCHEET REPORT.
		function getStudentConsolidatedMarksheet() {

			var reportType = '<?php echo $reportName; ?>';

			var tot_count = $("#total_count").val();
			var student_text = "";
			var i;
			//var tot_count = tot_count - 1;

			//alert(tot_count);

			var get_selected_checkbox = $('.checkbox_checked:checkbox:checked').length;

			for (i = 1; i <= tot_count; i++) {
				if (document.getElementById('get_report[' + i + ']').checked) {
					if (i == tot_count) {
						student_text += document.getElementById('get_report[' + i + ']').value;
					} else {
						student_text += document.getElementById('get_report[' + i + ']').value + ",";
					}

				}
			}


			if (get_selected_checkbox == 0) {
				var msg = 'Please select atleast one record';
				message_error(msg);
				return false;
			} else {
				if (reportType == 'CD1') {
					document.getElementById('res_student_text_pc').value = student_text;
					document.getElementById('student_consolidated_ug').submit();
				} else if (reportType == 'CD2') {
					document.getElementById('res_student_text_pc_pg').value = student_text;
					document.getElementById('student_consolidated_pg').submit();
				} else {
					var msg = 'Invalid Report Type';
					message_error(msg);
					return false;
				}
				return true;
			}
		}


		/*	SCRIPT FOR CHECK ALL CHECKBOX	*/
		$('.checkall').on('change', function() {
			var isChecked = $(this).prop("checked");
			var selector = $(this).data('target');
			$(selector).prop("checked", isChecked);
		});
	</script>
</body>

</html>