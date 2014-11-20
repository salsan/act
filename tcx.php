<?php
/* Copyright (C) 2014 Salvatore Santagati <salvatore.santagati@gmail.com> 
*/

class tcx {

	private $tcx;

	function __construct ( $act) {

		$this->tcx = new SimpleXMLElement("<TrainingCenterDatabase></TrainingCenterDatabase>");
		/* Namespace */
		$this->tcx->addAttribute('xmlns', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
		$this->tcx->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->tcx->addAttribute('xmlns:xsi:schemaLocation', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd');

		$Activities = $this->tcx->addChild( 'Activities' );
		$Activity = $Activities->addChild( 'Activity' );
		$Activity->addAttribute( 'Sport',  $act->getActivitySport() );  
		$Id = $Activity->addChild( 'Id', $act->getId() );
		$Lap = $Activity->addChild('Lap');
		$Lap->addAttribute( 'StartTime',  $act->getStarttime() );
		$TotalTimeSeconds = $Lap->addChild( 'TotalTimeSeconds', $act->getTotalTimeSeconds() );
		$DistanceMeters = $Lap->addChild ( 'DistanceMeters', $act->getDistanceMeters() );
		$Calories = $Lap->addChild ( 'Calories', $act->getCalories() );
		$AverageHeartRateBpm = $Lap->addChild ( 'AverageHeartRateBpm' );
		$AverageHeartRateBpm->addAttribute ('xmlns:xsi:type', 'HeartRateInBeatsPerMinute_t' );
		$Value = $AverageHeartRateBpm->addChild ( 'Value', $act->getAverageHeartRateBpm() );
		$MaximumHeartRateBpm = $Lap->addChild ('MaximumHeartRateBpm' );
		$MaximumHeartRateBpm->addAttribute ('xmlns:xsi:type', 'HeartRateInBeatsPerMinute_t' );
		$Value = $MaximumHeartRateBpm->addChild ( 'Value', $act->getMaxHearRate() );
		$Cadence = $Lap->addChild ('Cadence', $act->getAvgCadence() );
		$Track = $Lap->addChild ('Track');

		for ( $i = 0; $i < $act->getTracks(); $i++ ) {

			$Trackpoint = $Track->addChild('Trackpoint');
			$Time = $Trackpoint->addChild('Time', $act->getTimeTrack( $i ) );
			$Position = $Trackpoint->addChild('Position');
			$LatitudeDegrees = $Position->addChild('LatitudeDegrees', $act->getLatitude($i) );
			$LongitudeDegrees = $Position->addChild('LongitudeDegrees', $act->getLongitude($i) );
			$AltitudeMeters = $Trackpoint->addChild('AltitudeMeters', $act->getAltitude($i) );
			$HeartRateBpm = $Trackpoint->addChild('HeartRateBpm');
			$HeartRateBpm->addAttribute('xmlns:xsi:type','HeartRateInBeatsPerMinute_t');
			$Value = $HeartRateBpm->addChild('Value', $act->getHeartRate($i) );
			$Cadence = $Trackpoint->addChild('Cadence', $act->getCadenceTrack($i) );


		
		}

		$Creator = $Activity->addChild ('Creator');
		$Creator->addAttribute('xmlns:xsi:type', 'Device_t');
		$Name = $Creator->addChild ('Name', $act->getDeviceName());

	}

	function GetTcx ( ) {

		return $this->tcx;
	}
}



?>
