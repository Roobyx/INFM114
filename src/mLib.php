<?php

	class EventLink {
		protected $yearNow;
		protected $now;
		protected $dateUntil;
		protected $from;
		protected $to;
		protected $eventName;
		protected $eventDay;
		protected $eventDescription;
		protected $location;
		protected $reccuringRule;

		public function __construct($timeStart, $timeEnd, $eDay, $eName, $eDescription, $eLocation) {
			// Validate the time
			// if ($timeEnd < $timeStart) {
			// 	throw InvalidLink::negativeDateRange($timeStart, $timeEnd);
			// }

			$this->yearNow = date('Y');
			$this->now = date('Ymd');
			$this->dateUntil = $this->yearNow . "0421T210002Z";
			
			$this->from = $this->now .  "T" . substr(str_replace(':', '', $timeStart), 0, -2);
			$this->to = $this->now .  "T" . substr(str_replace(':', '', $timeEnd), 0, -2);

			$this->eventName = $eName;
			$this->eventDay = $eDay;
			$this->eventDescription = $eDescription;
			$this->location = $eLocation;

			$this->reccuringRule = "FREQ=WEEKLY;BYDAY=" . translateWeekDays($this->eventDay, 'BgToEnShort') . ";INTERVAL=1;UNTIL=" . $this->dateUntil;

		}

		public static function createEvent($timeStart, $timeEnd, $eventDay, $eventName, $eventDescription, $location) {
			return new static ($timeStart, $timeEnd, $eventDay, $eventName, $eventDescription, $location);
		}

		public function generateGoogleLink() {
			return "https://calendar.google.com/calendar/u/0/r/eventedit?dates=" . $this->from . "/" . $this->to . "&text=" . $this->eventName . "&" . $this->location . "&details=" . $this->eventDescription . "&recur=RRULE:" . $this->reccuringRule;
		}

		public function generateICSLink() {

			$SUMMARY = "SUMMARY:" . "\"" .$this->escapeString($this->eventName) . "\"\n";
			$DTSTART = "DTSTART:" . "\"" .$this->escapeString($this->from) . "\"\n";
			$DTEND = "DTEND:" . "\"" .$this->escapeString($this->to) . "\"\n";
			$DTSTAMP = "DTSTAMP:" . "\"" . $this->escapeString($this->from) . "\"\n";
			$RRULE = "RRULE:" . "\"" . $this->escapeString($this->reccuringRule) . "\"\n";
			$UID = "UID:" . "\"" . $this->generateICSUid() . "\"\n";
			$DESCRIPTION = "DESCRIPTION:" . "\"" . $this->escapeString($this->eventDescription) . "\"\n";
			// $DESCRIPTION = "DESCRIPTION: JOIN WEBEX MEETING https://meetingsemea3.webex.com/meetingsemea3/j.php?MTID=mfc5e5da4fc0e509d39a897b6078132deMeeting number (access code): 163 092 8616Meeting password: ZaB3HPNM7B3 (92234766 from video systems)JOIN BY PHONEUse VoIP onlyJOIN FROM A VIDEO SYSTEM OR APPLICATIONDial sip:1630928616@meetingsemea3.webex.comYou can also dial 62.109.219.4 and enter your meeting number.Join using Microsoft Lync or Microsoft Skype for BusinessDial sip:1630928616.meetingsemea3@lync.webex.comCan't join the meeting?https://collaborationhelp.cisco.com/article/WBX000029055IMPORTANT NOTICE: Please note that this Webex service allows audio and other information sent during the session to be recorded, which may be discoverable in a legal matter. By joining this session, you automatically consent to such recordings. If you do not consent to being recorded, discuss your concerns with the host or do not join the session.X-ALT-DESC;FMTTYPE=text/html:<style type=\"text/css\">table {	border-collapse: separate; width =100%;	border: 0;	border-spacing: 0;}tr {	line-height: 18px;}a, td {	font-size: 14px;font-family: Arial;color:#333;word-wrap: break-word;word-break: normal;padding: 0;}.title {font-size: 28px;}.image {width: auto;max-width: auto;}.footer {width: 604px;}.main {}@media screen and (max-device-width: 800px) {.title {font-size: 22px !important;}.image {width: auto !important;max-width: 100% !important;}.footer {width: 100% !important;max-width: 604px !important}.main {width: 100% !important;max-width: 604px !important}}</style><table bgcolor=\"#FFFFFF\" style=\"padding: 0; margin: 0; border: 0; width: 100%;\" align=\"left\"><tr style=\"height: 28p\"><td>&nbsp;</td></tr><tr><td align=\"left\" style=\"padding: 0 20px; margin: 0\"><!--<table bgcolor=\"#FFFFFF\" style=\"border: 0px; width: 100%; padding-left: 50px; padding-right: 50px;\" align=\"left\" class=\"main\"><tr><td align=\"center\" valign=\"top\" >&nbsp;</td></tr></table>--><table><tr><td><FONT SIZE=\"4\" COLOR=\"#666666\" FACE=\"arial\">When it's time, join the Webex meeting here.</FONT></td></tr></table>        <table>        <tr style=\"line-height: 20px;\"><td style=\"height:20px\">&nbsp;</td></tr><tr><td style=\"width:auto!important; \"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:auto;width:auto!important;background-color:#00823B; border:0px solid #00823B; border-radius:25px; min-width:160px!important;\"><tr><td align=\"center\" style=\"padding:10px 36px;\"><a href=\"https://meetingsemea3.webex.com/meetingsemea3/j.php?MTID=mfc5e5da4fc0e509d39a897b6078132de\" style=\"color:#FFFFFF; font-size:20px; text-decoration:none;\">Join meeting</a></td></tr></table></td></tr></table><table><tr style=\"line-height: 20px;\"><td style=\"height:20px\">&nbsp;</td></tr><tr><td><FONT SIZE=\"3\" COLOR=\"#666666\" FACE=\"arial\">More ways to join:</FONT></td>        </tr>            <tr style=\"line-height: 10px;\"><td style=\"height: 10px;\">&nbsp;</td></tr>        <tr><td><FONT SIZE=\"3\" COLOR=\"#666666\" FACE=\"arial\">Join from the meeting link</FONT></td>        </tr>        <tr><td><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\"><a href=\'https://meetingsemea3.webex.com/meetingsemea3/j.php?MTID=mfc5e5da4fc0e509d39a897b6078132de' style='color:#005E7D;  text-decoration:none; font-family: Arial;font-size: 14px;line-height: 24px;\'>https://meetingsemea3.webex.com/meetingsemea3/j.php?MTID=mfc5e5da4fc0e509d39a897b6078132de</a></FONT></td>        </tr>        <tr style=\"line-height: 20px;\"><td style=\"height:20px\">&nbsp;</td></tr><tr><td><FONT SIZE=\"3\" COLOR=\"#666666\" FACE=\"arial\">Join by meeting number</FONT></td>        </tr><tr><td><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\">Meeting number (access code): 163 092 8616</FONT></td></tr></table><table><tr><td><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\">Meeting password:</FONT></td><td><FONT SIZE=\"2\"  COLOR=\"#666666\" FACE=\"arial\">ZaB3HPNM7B3 (92234766 from video systems)</FONT></td></tr></table> <FONT size=\"2\" COLOR=\"#FF0000\" style=\"font-family: Arial;\"></FONT><FONT SIZE=\"4\" FACE=\"ARIAL\"><FONT SIZE=\"3\" COLOR=\"#666666\" FACE=\"arial\">Join by phone</FONT> &nbsp; <BR><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\">Use VoIP only</FONT>&nbsp; <BR><BR><BR><table><tr style=\"line-height: 20px;\"><td style=\"height:20px\">&nbsp;</td></tr></table><FONT SIZE=\"4\" FACE=\"ARIAL\"><FONT SIZE=\"3\" COLOR=\"#666666\" FACE=\"arial\">Join from a video system or application</FONT><BR><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\">Dial</FONT> <a href=\"sip:1630928616@meetingsemea3.webex.com\"><FONT SIZE=\"2\" COLOR=\"#005E7D\" FACE=\"arial\">1630928616@meetingsemea3.webex.com</FONT></a>&nbsp; <BR><FONT SIZE=\"2\" COLOR=\"#666666\" FACE=\"arial\">You can also dial 62.109.219.4 and enter your meeting number.</FONT> &nbsp; <BR></FONT>&nbsp; <BR><table><tr style=\"line-height: 20px;\"><td style=\"height:20px\">&nbsp;</td></tr></table><table cellpadding=\"0\" cellspacing=\"0\"><tr><td  style=\"color: #000000; font-family: Arial;font-size: 12px; font-weight: bold; line-height: 24px;\"><b>Join using Microsoft Lync or Microsoft Skype for Business</b></td></tr><tr style=\"margin:0px\"><td style=\"color: #333333; font-family: Arial; font-size: 14px; line-height: 24px;\">Dial <a href=\" sip:1630928616.meetingsemea3@lync.webex.com\"   style=\"text-decoration:none;color:#005E7D\">1630928616.meetingsemea3@lync.webex.com</a></td></tr></table><table><tr style=\"line-height: 20px\"><td style=\"height:20px\">&nbsp;</td></tr></table><table style=\"width: 100%;\" align=\"left\" class=\"main\">                <tr style=\"height: 20px\"><td>&nbsp;</td></tr><tr><td style=\"height: 24px; color: #000000; font-family:Arial; font-size: 14px; line-height: 24px;\">Need help? Go to <a href=\"https://help.webex.com\" style=\"color:#005E7D; text-decoration:none;\">https://help.webex.com</a></td></tr>                <tr style=\"height: 44px\"><td>&nbsp;</td></tr></table></td></tr></table>";

			$LOCATION = "";

			if($this->location) {
				$LOCATION = "LOCATION:" . "\"" . $this->escapeString($this->location) . "\"\n";
			}

			return "
				BEGIN:VCALENDAR \n
				VERSION:2.0\n
				PRODID:-//SERN//INDICO//EN \n
				BEGIN:VEVENT\n" .
				$SUMMARY .
				"TZID:Europe/Sofia\n" .
				$DTSTART .
				$DTEND .
				$DTSTAMP .
				$RRULE .
				$UID .
				$DESCRIPTION .
				$LOCATION .
				"END:VEVENT\n
				END:VCALENDAR";



			// BEGIN:VCALENDAR
			// VERSION:2.0
			// PRODID:-//SERN//INDICO//EN
			// BEGIN:VEVENT
			// SUMMARY:Software Meeting
			// TZID:Europe/Zurich
			// DTSTART:20150202T170000
			// DTEND:20150202T180000
			// DTSTAMP:20150202T170000
			// RRULE:FREQ=WEEKLY;UNTIL=20380119T000000
			// UID:indico-event-565483@sern.ch
			// DESCRIPTION:https://indico.sern.ch/event/999999/
			// LOCATION:42-3-002 (SERN)
			// URL:https://indico.sern.ch/event/999999/
			// END:VEVENT
			// END:VCALENDAR
		}

		protected function generateICSUid(): string {
			return md5(sprintf(
				'%s%s',
					$this->eventName,
					$this->location
			));
		}

		protected function escapeString(string $field): string {
			return addcslashes($field, "\r\n,;");
		}
	}

?>