truncate table `alimentc_nazdys`.`plan_entegre`;
INSERT INTO `alimentc_nazdys`.`plan_entegre` (
    `plan_entegre`.`yatay`,
    `plan_entegre`.`dikey`,
    `plan_entegre`.`oran` )          
SELECT `entegreduzeyleri`.`yatay`,
    `entegreduzeyleri`.`dikey`,
    `entegreduzeyleri`.`oran`
FROM `alimentc_easynet`.`entegreduzeyleri`;