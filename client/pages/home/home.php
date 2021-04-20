<?php
	include "./src/main.php";
?>

<section class="user-box second-box">
		<!-- Show all courses -->

		<div class="card activity">
			<span class='status phrase-accent make-space-v'> Текуща седмица: </span> 
			<?php 
				if(isset($_SESSION['currentSemesterWeek'])) {
					echo $_SESSION['currentSemesterWeek'];
				} else {
					getCurrentCurrentSemesterInfo();
					echo $_SESSION['currentSemesterWeek'];
				}
			?>
		</div>

		<div class="card activity">
			<span class='status phrase-accent make-space-v'> Текущ семестър: </span>
			<?php 
				if(isset($_SESSION['currentSemester'])) {
					echo $_SESSION['currentSemester'];
				} else {
					getCurrentCurrentSemesterInfo();
					echo $_SESSION['currentSemester'];
				}
			?>
		</div>

		<div class="card activity">
			<span class='status phrase-accent make-space-v'> Предстояща сесия:  </span>
			<?php 
				if(isset($_SESSION['upcomingTerm'])) {
					echo $_SESSION['upcomingTerm'];
				} else {
					getCurrentCurrentSemesterInfo();
					echo $_SESSION['upcomingTerm'];
				}
			?>
		</div>

		<div class="card activity">
			<span class='status phrase-accent make-space-v'> Опресни данните:  </span>
			
			<form method="post" action="home" method="post">
				<button class='button offer-button' name='resetVirginStatus'> Старт </button>
			</form>
		</div>
</section>

<?php if(!$_SESSION['virgin']) { ?>
	<section class="user-box second-box">
		<div class="cards-wrapper">
				<!-- Show all courses -->
			<div class="cards-header">
				<div class="cards-view">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<path d="M16 2v4M8 2v4M3 10h18"></path>
					</svg>

					График за: <?php echo $_SESSION['latestSemester']; ?>
				</div>

				
				<div class='in-table-cta'>
					Легенда: <span class="status green make-space-h">Днес</span>
				</div>

			</div>

			<div class="card">
				<table class="table schedule-table">

					<?php
						if(isset($_SESSION['schedule'])) {
							getSessionSchedule($dbLink, 'courses');
						} else {
							setSessionSchedule($dbLink, 'courses');
							getSessionSchedule($dbLink, 'courses');
						}
					?>

				</table>
			</div>

		</div>

	</section>

	<section class="user-box second-box">

		<div class="cards-wrapper">
			<!-- Show all courses -->
			<div class="cards-header">
				<div class="cards-view">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<path d="M16 2v4M8 2v4M3 10h18"></path>
					</svg>

					Проекти за: <?php echo $_SESSION['latestSemester']; ?>
				</div>
			</div>

			<div class="card">
				<table class="table schedule-table">

					<?php
						if(isset($_SESSION['schedule'])) {
							getSessionSchedule($dbLink, 'courseWorks');
						} else {
							setSessionSchedule($dbLink, 'courseWorks');
							getSessionSchedule($dbLink, 'courseWorks');
						}
					?>

				</table>
			</div>
		</div>
	</section>

<?php } else { ?>

	<section class='user-box'>
		<div class="modal-hld <?php echo $_SESSION['virgin'] ? '' : 'hidden'; ?>">
			<div class="card modal">
				<div class="title"> Интеграция: </div>
				<div>
					<p> Все още не сте взели данните от вашият student.nbu.bg профил. </p>
					<p> Моля въведете вашата парола за student.nbu.bg за да продължите.* </p>

					<form method="post" action="home" method="post">
						<!-- <label for="eStudentPass"> Моля въведете паролата си за e-student.nbu.bg</label> -->
						<input name="eStudentPass" type="password" placeholder="Е-Студент парола">

						<br>
						<button class='button offer-button' name='getEstudentData'>
							Готово
						</button>
					</form>

					<p> *Вашата парола няма да бъде запазена никъде. Въпреки това, Ви препоръчваме да смените вашата първоначална парола в student.nbu.bg ако все още не сте, тъй като тя е вашето ЕГН.</p>
				</div>
			</div>
		</div>
	</section>

<?php } ?>