<?php 
header("Content-type: image/png");
$img = imagecreate(275,132);
$black = ImageColorAllocate($img, 0, 0, 0);
$green = ImageColorAllocate($img, 0, 255, 0);
$white = ImageColorAllocate($img, 255, 255, 255);
$trans = ImageColorTransparent($img, $white);
ImageFill($img, 0, 0, $black);
ImageString($img , 2, 10, 5, "1: ". $_GET['code1'], $white);
ImageString($img , 2, 10, 17, "2: ". $_GET['code2'], $white);
ImageString($img , 2, 10, 29, "3: ". $_GET['code3'], $white);
ImageString($img , 2, 10, 41, "4: ". $_GET['code4'], $white);
ImageString($img , 2, 10, 53, "5: ". $_GET['code5'], $white);
ImageString($img , 2, 10, 65, "6: ". $_GET['code6'], $white);
ImageString($img , 2, 10, 77, "7: ". $_GET['code7'], $white);
ImageString($img , 2, 10, 89, "8: ". $_GET['code8'], $white);
ImageString($img , 2, 10, 101, "9: ". $_GET['code9'], $white);
ImageString($img , 2, 10, 113, "10: ". $_GET['code10'], $white);

ImageString($img , 2, 100, 5, "11: ". $_GET['code11'], $white);
ImageString($img , 2, 100, 17, "12: ". $_GET['code12'], $white);
ImageString($img , 2, 100, 29, "13: ". $_GET['code13'], $white);
ImageString($img , 2, 100, 41, "14: ". $_GET['code14'], $white);
ImageString($img , 2, 100, 53, "15: ". $_GET['code15'], $white);
ImageString($img , 2, 100, 65, "16: ". $_GET['code16'], $white);
ImageString($img , 2, 100, 77, "17: ". $_GET['code17'], $white);
ImageString($img , 2, 100, 89, "18: ". $_GET['code18'], $white);
ImageString($img , 2, 100, 101, "19: ". $_GET['code19'], $white);
ImageString($img , 2, 100, 113, "20: ". $_GET['code20'], $white);

ImageString($img , 2, 190, 5, "21: ". $_GET['code21'], $white);
ImageString($img , 2, 190, 17, "22: ". $_GET['code22'], $white);
ImageString($img , 2, 190, 29, "23: ". $_GET['code23'], $white);
ImageString($img , 2, 190, 41, "24: ". $_GET['code24'], $white);
ImageString($img , 2, 190, 53, "25: ". $_GET['code25'], $white);
ImageString($img , 2, 190, 65, "26: ". $_GET['code26'], $white);
ImageString($img , 2, 190, 77, "27: ". $_GET['code27'], $white);
ImageString($img , 2, 190, 89, "28: ". $_GET['code28'], $white);
ImageString($img , 2, 190, 101, "29: ". $_GET['code29'], $white);
ImageString($img , 2, 190, 113, "30: ". $_GET['code30'], $white);

ImageTTFText($img, 18, 0, 45, 45, $green, "opensans.ttf", "$text");
ImagePng($img);
ImageDestroy($img); 
?>