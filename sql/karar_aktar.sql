truncate table `alimentc_nazdys`.`plan_karar`;
INSERT INTO `alimentc_nazdys`.`plan_karar` (
    `plan_karar`.`planno`,
    `plan_karar`.`degerlendirmekarartarih`, 
    `plan_karar`.`uye1adi`, 
    `plan_karar`.`uye2adi`, 
    `plan_karar`.`uye3adi`, 
    `plan_karar`.`uye4adi`, 
    `plan_karar`.`uyeikuadi`, 
    `plan_karar`.`kararaciklama`, 
    `plan_karar`.`belgedurum`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`degerlendirmekarartarih`, 
    `splanlar`.`uye1adi`, 
    `splanlar`.`uye2adi`, 
    `splanlar`.`uye3adi`, 
    `splanlar`.`uye4adi`, 
    `splanlar`.`uyeikuadi`, 
    `splanlar`.`kararaciklama`, 
    `splanlar`.`belgedurum`
FROM `alimentc_easynet`.`splanlar`;