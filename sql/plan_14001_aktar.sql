truncate table `alimentc_nazdys`.`plan_14001`;
INSERT INTO `alimentc_nazdys`.`plan_14001` (
    `plan_14001`.`planno`,
    `plan_14001`.`iso14001hamsure`, 
    `plan_14001`.`iso14001indart`, 
    `plan_14001`.`iso14001indartsure`, 
    `plan_14001`.`iso14001entindart`, 
    `plan_14001`.`iso14001kalansure`, 
    `plan_14001`.`iso14001a1sure`, 
    `plan_14001`.`iso14001a2sure`, 
    `plan_14001`.`iso14001gsure`,
    `plan_14001`.`iso14001ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso14001hamsure`, 
    `splanlar`.`iso14001indart`, 
    `splanlar`.`iso14001normalsure`, 
    `splanlar`.`iso14001entindart`, 
    `splanlar`.`iso14001kalansure`, 
    `splanlar`.`iso14001a1sure`, 
    `splanlar`.`iso14001a2sure`, 
    `splanlar`.`iso14001gsure`,
    `splanlar`.`iso14001ybsure`
FROM `alimentc_easynet`.`splanlar`;