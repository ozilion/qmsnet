truncate table `alimentc_nazdys`.`plan_sertifika`;
INSERT INTO `alimentc_nazdys`.`plan_sertifika` (
    `plan_sertifika`.`planno`,
    `plan_sertifika`.`belgeno`, 
    `plan_sertifika`.`ilkyayin`, 
    `plan_sertifika`.`yayintarihi`, 
    `plan_sertifika`.`gecerliliktarihi`, 
    `plan_sertifika`.`bitistarihi`, 
    `plan_sertifika`.`akreditasyon`, 
    `plan_sertifika`.`durum`)          
SELECT `sertifikalar`.`planno`,
    `sertifikalar`.`certno`, 
    `sertifikalar`.`ilkyayin`, 
    `sertifikalar`.`ilkyayintarihi`, 
    `sertifikalar`.`bitistarihi`, 
    `sertifikalar`.`bittarihi`, 
    `sertifikalar`.`akreditasyon`, 
    `sertifikalar`.`durum`
FROM `alimentc_easynet`.`sertifikalar`;