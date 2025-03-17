truncate table `alimentc_nazdys`.`plan_45001`;
INSERT INTO `alimentc_nazdys`.`plan_45001` (
    `plan_45001`.`planno`,
    `plan_45001`.`iso45001hamsure`, 
    `plan_45001`.`iso45001indart`, 
    `plan_45001`.`iso45001indartsure`, 
    `plan_45001`.`iso45001entindart`, 
    `plan_45001`.`iso45001kalansure`, 
    `plan_45001`.`iso45001a1sure`, 
    `plan_45001`.`iso45001a2sure`, 
    `plan_45001`.`iso45001gsure`,
    `plan_45001`.`iso45001ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso45001hamsure`, 
    `splanlar`.`iso45001indart`, 
    `splanlar`.`iso45001normalsure`, 
    `splanlar`.`iso45001entindart`, 
    `splanlar`.`iso45001kalansure`, 
    `splanlar`.`iso45001a1sure`, 
    `splanlar`.`iso45001a2sure`, 
    `splanlar`.`iso45001gsure`,
    `splanlar`.`iso45001ybsure`
FROM `alimentc_easynet`.`splanlar`;