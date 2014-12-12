<?php

/* Copyright (C) 2014 Salvatore Santagati <salvatore.santagati@gmail.com> 
 */

class act2tcx {

	private $Sport_t;
	private $Id;
	private $dateTime;
	private $ttseconds;
	private $distancemeters;
	private $AvgHeartRate;
	private $MaxHearRate;
	private $Cadence;
	private $Tracks;
	private $TimeTrack;
	private $track;
	private $LatitudeDegrees;
	private $LongitudeDegrees;
	private $AltitudeMeters;
	private $HeartRateBpm;
	private	$CadenceTrack;
	private $Device;
	private $Distance;




	function __construct ( $act ) {
			

		$this->setActivitySport ( $act );
		$this->setId ( $act );
		$this->setStarttime ( $act );
		$this->setTotalTimeSeconds ( $act );
		$this->setDistanceMeters ( $act );
		$this->setCalories ( $act );
		$this->setAverageHeartRateBpm ($act);
		$this->setMaxHearRate ($act);
		$this->setAvgCadence ($act);
		$this->setTracks ($act);
		$this->setTrackPoints ( $act );
		$this->setDeviceName ( $act );
	
	}

	function setDistance ( $lat1, $lon1, $lat2, $lon2, $speed, $interval ) {

		$this->distance = 0;

		if (( $lat1 != $lat2 ) && ( $lon1 != $lon2 ))
		{
			// Get distance from longitude and latitude
			// Haversine formula

    			$earth_radius = 6371;  
      
    			$dLat = deg2rad($lat2 - $lat1);  
    			$dLon = deg2rad($lon2 - $lon1);  
      
    			$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);  
			$c = 2 * asin(sqrt($a));  

			$DistHaversine = $earth_radius * $c;

			// Get distance from speed and interval 
			$DistInterval  = ( ( $speed / 60 ) / 60 ) * $interval;

			// Media distance result
			if ( $DistInterval == 0 )
				$this->distance = $DistHaversine * 1000;
			else 	$this->distance = ( ( $DistHaversine + $DistInterval ) / 2 ) * 1000;

				
		}
		

		
		return $this->distance;  
	
	
	}

	function hoursToSeconds ($hour) { 
	
		$hour_fixed = strtotime(str_replace(".", ":" , $hour ));

		$hours 	= date('H', $hour_fixed);
		$mins= date('i', $hour_fixed);
		$secs= date('s', $hour_fixed);

		return $hours * 3600 + $mins * 60 + $secs;
	}

	function setActivitySport ( $act ) 	{

		
		switch ($act->trackmaster->Sport1) {

		case	0:
		case	1:
		case	2: 
				$this->Sport_t = "Running";
				break;
		case	3:	$this->Sport_t = "Biking";
				break;
		case	4:
		case	5:	$this->Sport_t = "Other";
				break;
		default :
				$this->Sport_t = "";
	
		}

	}

	function setDeviceName ( $act ) {
		
		$this->Device = $act->getName();

	}

	function setId ( $act )		{

		$trackName = date("Y-m-d", strtotime($act->trackmaster->TrackName));

		$this->Id = $trackName . "T" .  date('H:i:s', strtotime($act->trackmaster->StartTime)) . "Z";
		
	}

	function setStarttime ( $act )	{
		
		$trackName = date("Y-m-d", strtotime($act->trackmaster->TrackName));

		$this->dateTime =  $trackName . "T" . date('H:i:s', strtotime($act->trackmaster->StartTime)) . "Z";
	}



	function setTotalTimeSeconds ( $act ) {
	
		$this->ttseconds =  ( $this->hoursToSeconds ( $act->trackmaster->Duration ) );

	}

	function setDistanceMeters ( $act )	{

		$this->Distancemeters = $act->trackmaster->TotalDist;
	
	
	}

	function setCalories ( $act ) {

		$this->Calories = $act->trackmaster->Calories;
	}	


	function setAverageHeartRateBpm ( $act ) {
		
		$this->AvgHeartRate = $act->trackmaster->AvgHeartRate;
	}

	function setMaxHearRate ($act) {

		$this->MaxHearRate = $act->trackmaster->MaxHearRate;
	
	}

	function setAvgCadence ( $act ) {

		$this->Cadence	=  $act->trackmaster->AvgCadence;
	}

	function setTracks ( $act ){

		$this->Tracks = count ($act->TrackPoints );
	}

	function setTimeTrack ( $act, $track , $value ){
 
		$this->TimeTrack[$track] = $value;
	
	}

	

	function setTrackPoints( $act ){
		
		$this->CurrentTime = new DateTime ($this->getStarttime()) ;
		$this->IntervalTimeDiff = 0;
		$this->Distance[0] = 0;
		
		for ( $this->track = 0; $this->track < $this->getTracks (); $this->track++) {
			
		       /* TIME */
		       $this->TimeTrack[$this->track] = $this->CurrentTime->format('Y-m-d\TH:i:s\Z');
		       /* Fix Format of intervaltime */
		       $this->IntervalTime =  round ( str_replace(",", ".",  $act->TrackPoints[$this->track]->IntervalTime ) + $this->IntervalTimeDiff );
		       $this->IntervalTimeDiff = str_replace(",", ".", $act->TrackPoints[$this->track]->IntervalTime ) -  $this->IntervalTime + $this->IntervalTimeDiff;
		       $this->CurrentTime->add(new DateInterval('PT' . $this->IntervalTime . 'S'));

		       /* Latitude */
		       $this->LatitudeDegrees[$this->track] = ( str_replace(",", "." , $act->TrackPoints[$this->track]->Latitude ) );

		       /* Longitude */
		       $this->LongitudeDegrees[$this->track] = ( str_replace(",", "." , $act->TrackPoints[$this->track]->Longitude ) );

		       /* Altitude */
		       $this->AltitudeMeters[$this->track] = $act->TrackPoints[$this->track]->Altitude;
		     
		       /* Distance */
		       if ( $this->track > 0 )
		       	{
				$this->Distance[$this->track] = $this->Distance[$this->track-1] + 
								$this->setDistance ( 
									$this->LatitudeDegrees[$this->track],
									$this->LongitudeDegrees[$this->track],
									$this->LatitudeDegrees[$this->track-1],
									$this->LongitudeDegrees[$this->track-1],
									str_replace (",",".",$act->TrackPoints[$this->track]->Speed),
									str_replace (",",".",$act->TrackPoints[$this->track]->IntervalTime)
								) ;

			}
		
		       /* HeartRate */
		       $this->HeartRateBpm[$this->track] = $act->TrackPoints[$this->track]->HeartRate;

		       /* Cadence */
		       $this->CadenceTrack[$this->track] = $act->TrackPoints[$this->track]->Cadence;
			

		}
	}

	function setLatitude( $act, $track , $value) {
		$this->LatitudeDegrees[$track] = $value;
	}
	
	function setLongitude( $act, $track , $value){
		$this->LongitudeDegrees[$track] = $value;
	}

	function setAltitude($act, $track , $value){
		
		$this->AltitudeMeters[$track] = $value;
	
	}

	function setHeartRate($act, $track , $value){

		$this->HeartRate[$track] = $value;
	}

	function setCadenceTrack ($act, $track, $value) {
		
		$this->CadenceTrack[$track] = $value;
	}


	function getDeviceName ( ) {

		return $this->Device;
	}

	
	function getCadenceTrack ($track) {

		return $this->CadenceTrack[$track];

	}


	function getHeartRate ($track){
		return $this->HeartRateBpm[$track];
	}

	function getAltitude($track){
		return $this->AltitudeMeters[$track];
	}
	
	function getLongitude($track){
		return $this->LongitudeDegrees[$track];
	}
	
	function getDistance($track){
		return $this->Distance[$track];
	}

	
	function getLatitude($track) {

		return $this->LatitudeDegrees[$track];
			
	}

	function getTimeTrack ( $track ){

		return $this->TimeTrack[$track];
	}

	function getTracks () {
		
		return $this->Tracks;

	}

	function getAvgCadence () {
		
		return $this->Cadence;

	}

	function getMaxHearRate () {

		return $this->MaxHearRate;
	
	}

	function getAverageHeartRateBpm ( )  {

		return $this->AvgHeartRate;
	}

	function getCalories (  ) {

		return $this->Calories;
	}
	function getDistanceMeters (  ) {

		return $this->Distancemeters;
	
	}

	function getTotalTimeSeconds () {

		return $this->ttseconds;

	}


	function getStarttime()		{

		return $this->dateTime;
	}	


	
	function getId ()		{

		return $this->Id;
	}

	function getActivitySport()	{

		return $this->Sport_t;
	
	}


}

?>
