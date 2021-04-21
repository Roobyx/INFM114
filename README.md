# GOAL:

Simple PHP project made as a coursework New Bulgarian University course **INFM114**.
The idea is to manage the data that is available in e-student.nbu.bg in an eaiser and nicer manner - effectively a manager for the student website.

Please be advice that the project requries an active account in e-student.nbu.bg and it does require a password input once to get the data. The password is not being saved and the project does not take any responsibility. The code is public and transparent.

Don't judge it, I dont usually write in PHP and this is what it is.
It's on Github mainly for ease of work for me. It's public, because why not :D

# BACKLOG:

1. ~~`Build one page structure`~~
2. ~~`Use Register / login / password change`~~
3. ~~`Debug e-student website and create a way to login and scrape needed info`~~
4. ~~`Update the DB to handle more than the user's login credentials`~~
5. ~~`Build some UI`~~
6. ~~`Offer the option to supply eStudent password to scrape data. Make it so this is hidden if data is already scrapped.`~~
7. ~~`Insert gathered data to the DB`~~
8. ~~`Display the student' schedule in a nice way on the dashboard`~~
9.  ~~`Generate links for events for each couse to be added to calendars`~~
	- ~~` Google calendar`~~
	
# Optional
11. ~~` Add current week number`~~
12. ~~` Scrape NBU Calendar and add warnings for upcoming term`~~
13. Add coursework and test dates
14. ~~`Add "Report bug" feature`~~

# Random additional ideas (Contributions are welcome)
- If courseworks feature is added - add a page for each course and add them to the DB
- Add a way to drop the credentials (from DB) in the profile/settings page (not priority as there is no actual personal data saved, but good to have)
- Add a way to send emails as reminders for lectures (or events if such feature is built)
- Add a way to send browser/system notifications for lecture or events
- For each course add editable fields - "requirements", "coursework details", "tests date", "additional notes", "completed"
- Add a share feature for the course, that would display a public page for it
- With information from the "rating/Ratings.aspx" add auto-completed feature coloring the course + the grade
- Currently the sample semester info is only for regular students, there is no support for distance learning students
