<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pie</title>
</head>

<body>


<?php 
    /******************************** 
      2D Pie Chart Version 1.0 
      Programer: Xiao Bin Zhao 
      Date: 03/31/2001 
      All Rights Reserved 2001. 
    ********************************/ 
     
    /*************Configuration Starts Here******************/ 
    $chartTitle = "Most Popular Stars";            //pie chart name 
    $logo = "www.asianstar.com";                   //logo 
    /*************************End****************************/ 
     
    /*****************For Programers Only********************/ 
    $imageWidth = 300;                             //image width  
    $imageHeight = 200;                            //image height 
    $diameter = 150;                               //pie diameter  
    $centerX = 100;                                //pie center pixels x 
    $centerY = 100;                                //pie center pixels y 
    $labelWidth = 10;                              //label width, no need to change  
    /*************************End****************************/ 
     
    $data = array( 156, 20, 80, 100, 124, 56, 34, 32, 67 ); 
    $item = array( "Vivian", "Alan", "Valen", "Chow", "Chen", "COCO", "SiSi", "Lee", "Zhao" ); 
    for( $i = 0; $i < count( $data ); $i++ ) 
    { 
        $dataTotal += $data[ $i ]; 
    } 
     
    function circlePoint( $deg, $dia ) 
    { 
        $x = cos( deg2rad( $deg ) ) * ( $dia / 2 ); 
        $y = sin( deg2rad( $deg ) ) * ( $dia / 2 );     
        return array( $x, $y ); 
    } 
     
    $im = ImageCreate( $imageWidth, $imageHeight ); 
     
    $color[] = ImageColorAllocate( $im, 255, 0, 0 ); //red 
    $color[] = ImageColorAllocate( $im, 255, 204, 0 );//yellow 
    $color[] = ImageColorAllocate( $im, 153, 204, 0 );//green 
    $color[] = ImageColorAllocate( $im, 153, 51, 255 );//purple 
    $color[] = ImageColorAllocate( $im, 0, 128, 255 );//blue 
    $color[] = ImageColorAllocate( $im, 255, 0, 128 );//pink 
    $color[] = ImageColorAllocate( $im, 153, 51, 255 );//purple 
    $color[] = ImageColorAllocate( $im, 192, 192, 192 );//grey 
    $color[] = ImageColorAllocate( $im, 204, 204, 0 ); 
    $color[] = ImageColorAllocate( $im, 64, 128, 128 ); 
    $color[] = ImageColorAllocate( $im, 204, 102, 153 ); 
    $white = ImageColorAllocate( $im, 255, 255, 255 ); 
    $black = ImageColorAllocate( $im, 0, 0, 0 ); 
    $grey = ImageColorAllocate( $im, 215, 215, 215 ); 

    ImageFill( $im, 0, 0, $white ); 
     
    $degree = 0; 
    for( $i = 0; $i < count( $data ); $i++ ) 
    { 
        $startDegree = round( $degree ); 
        $degree += ( $data[ $i ] / $dataTotal ) * 360; 
        $endDegree = round( $degree ); 
         
        $currentColor = $color[ $i % ( count( $color ) ) ]; 
         
        ImageArc( $im, $centerX, $centerY, $diameter, $diameter, $startDegree, $endDegree, $currentColor ); 
         
        list( $arcX, $arcY ) = circlePoint( $startDegree, $diameter ); 
        ImageLine( $im, $centerX, $centerY, floor( $centerX + $arcX ), floor( $centerY + $arcY ), $currentColor ); 
         
        list( $arcX, $arcY ) = circlePoint( $endDegree, $diameter ); 
        ImageLine( $im, $centerX, $centerY, ceil( $centerX + $arcX ), ceil( $centerY + $arcY ), $currentColor ); 
         
        $midPoint = round( ( ( $endDegree - $startDegree ) / 2 ) + $startDegree ); 
        list( $arcX, $arcY ) = circlePoint( $midPoint, $diameter / 1.5 ); 
        ImageFillToBorder( $im, floor( $centerX + $arcX ), floor( $centerY + $arcY ), $currentColor, $currentColor ); 
        ImageString( $im, 2, floor( $centerX + $arcX ), floor( $centerY + $arcY ), intval( round( $data[ $i ] / $dataTotal * 100 ) ) . "%", $black ); 
    } 
     
    $labelX = $centerX + $diameter / 2 + 10; 
    $labelY = $centerY - $diameter / 4; 
    $titleX = $labelX - $diameter / 4; 
    $titleY = $centerY - $diameter / 2; 
    ImageString( $im, 3, $titleX + 1, $titleY + 1, $chartTitle, $grey ); 
    ImageString( $im, 3, $titleX, $titleY, $chartTitle, $black ); 
    ImageString( $im, 1, $labelX, $titleY + 14, date( "Y-m-d H:i:sa" ), $black ); 
     
    for( $i = 0; $i < count( $item ); $i++ ) 
    { 
        $currentColor = $color[ $i % ( count( $color ) ) ]; 
        ImageRectangle( $im, $labelX, $labelY, $labelX + $labelWidth, $labelY + $labelWidth, $black ); 
        ImageFilledRectangle( $im, $labelX + 1, $labelY + 1, $labelX + $labelWidth, $labelY + $labelWidth, $currentColor ); 
        ImageString( $im, 2, $labelX + $labelWidth + 5, $labelY, $item[ $i ], $black ); 
        ImageString( $im, 2, $labelX + $labelWidth + 60, $labelY, $data[ $i ], $black ); 
        $labelY += $labelWidth + 2; 
    } 
    ImageString( $im, 3, $labelX, $labelY, "Total:", $black ); 
    ImageString( $im, 3, $labelX + $labelWidth + 60, $labelY, $dataTotal, $black ); 
    ImageString( $im, 2, $labelX, $labelY + 15, $logo, $black ); 
    Header( "Content-type: image/PNG" ); 
    ImagePNG( $im ); 
    ImageDestroy( $im ); 
?>

</body>
</html>