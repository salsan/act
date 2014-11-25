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

	function hoursToSeconds ($hour) { // $hour must be a string type: "HH:mm:ss"

		$parse = array();

    		if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}).(?<secs>[\d]{2})$#',$hour,$parse)) {
         	// Throw error, exception, etc
        		throw new RuntimeException ("Hour Format not valid");
    		}

		return $parse['hours'] * 3600 + $parse['mins'] * 60 + $parse['secs'];
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

		$this->Id = $act->trackmaster->TrackName . "T" .  date('H:i:s', strtotime($act->trackmaster->StartTime)) . "Z";
		
	}

	function setStarttime ( $act )	{

		$this->dateTime =  $act->trackmaster->TrackName . "T" . date('H:i:s', strtotime($act->trackmaster->StartTime)) . "Z";
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
