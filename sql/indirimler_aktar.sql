truncate table `alimentc_nazdys`.`azaltmaarttirmalar`;
INSERT INTO `alimentc_nazdys`.`azaltmaarttirmalar` (
    `azaltmaarttirmalar`.`standart`,
    `azaltmaarttirmalar`.`baslik`,
    `azaltmaarttirmalar`.`name`, 
    `azaltmaarttirmalar`.`sira`, 
    `azaltmaarttirmalar`.`oran`)          
SELECT 
    `indirimler`.`standard`,
    `indirimler`.`baslik`,
    `indirimler`.`name`, 
    `indirimler`.`sira`, 
    `indirimler`.`oran`
FROM `alimentc_easynet`.`indirimler`;

UPDATE `alimentc_nazdys`.`azaltmaarttirmalar` SET standart="down" where standart="İNDİRİMLER";
UPDATE `alimentc_nazdys`.`azaltmaarttirmalar` SET standart="up" where standart="ARTTIRIMLAR";
