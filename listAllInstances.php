#!/usr/bin/php -d safe_mode=Off
<?php

function TrouverNom(array $aTags)
{
	foreach($aTags as $oTag) {
		if($oTag->Key === 'Name') return $oTag->Value;
	}
	return 'unamed';
}

function TrouverImage($sImageId)
{
	$aImages = array(
        'ami-e079f893' => 'debian-8.4',
        'ami-eb018598' => 'ubuntu-16.04',
        'ami-25158352' => 'rhel-7.1',
		'ami-8b8c57f8' => 'RHEL 7.2',
        'ami-98e114f8' => 'debian-8.4',
        'ami-4dbf9e7d' => 'rhel-7.1',
	);

	if( ! isset($aImages[$sImageId])) 
		return 'inconnue (' . $sImageId . ')';
	else return $aImages[$sImageId];

}

$aRegions = array();
$sCmd = 'aws ec2 describe-regions';
$a = json_decode(shell_exec($sCmd));
foreach($a->Regions as $oRegion) {
	$aRegions[] = $oRegion->RegionName;
}

foreach($aRegions as $sRegion) {
	echo '# ' . $sRegion . "\n";
	$sCmd = 'aws ec2 --region ' . $sRegion .' describe-instances';
	$a = json_decode(shell_exec($sCmd));
	foreach($a->Reservations as $oReservation) {
		foreach($oReservation->Instances as $oInstance) {
			printf("%s\t%s\t%s\t%s\t%s\t%s\t%s\n", 
				$oInstance->InstanceId,
				TrouverNom($oInstance->Tags), 
				$oInstance->State->Name, 
				$oInstance->InstanceType,
				TrouverImage($oInstance->ImageId),
				$oInstance->PublicIpAddress, 
				$oInstance->PrivateIpAddress);
		}
	}
}