truncate table `alimentc_nazdys`.`plan_50001`;
INSERT INTO `alimentc_nazdys`.`plan_50001` (
    `plan_50001`.`planno`,
    `plan_50001`.`iso50001hamsure`, 
    `plan_50001`.`iso50001indart`, 
    `plan_50001`.`iso50001indartsure`, 
    `plan_50001`.`iso50001entindart`, 
    `plan_50001`.`iso50001kalansure`, 
    `plan_50001`.`iso50001a1sure`, 
    `plan_50001`.`iso50001a2sure`, 
    `plan_50001`.`iso50001gsure`,
    `plan_50001`.`iso50001ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso50001hamsure`, 
    `splanlar`.`iso50001indart`, 
    `splanlar`.`iso50001normalsure`, 
    `splanlar`.`iso50001entindart`, 
    `splanlar`.`iso50001kalansure`, 
    `splanlar`.`iso50001a1sure`, 
    `splanlar`.`iso50001a2sure`, 
    `splanlar`.`iso50001gsure`,
    `splanlar`.`iso50001ybsure`
FROM `alimentc_easynet`.`splanlar`;