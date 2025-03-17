truncate table `alimentc_nazdys`.`plan_9001`;
INSERT INTO `alimentc_nazdys`.`plan_9001` (
    `plan_9001`.`planno`,
    `plan_9001`.`iso9001hamsure`, 
    `plan_9001`.`iso9001indart`, 
    `plan_9001`.`iso9001indartsure`, 
    `plan_9001`.`iso9001entindart`, 
    `plan_9001`.`iso9001kalansure`, 
    `plan_9001`.`iso9001a1sure`, 
    `plan_9001`.`iso9001a2sure`, 
    `plan_9001`.`iso9001gsure`,
    `plan_9001`.`iso9001ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso9001hamsure`, 
    `splanlar`.`iso9001indart`, 
    `splanlar`.`iso9001normalsure`, 
    `splanlar`.`iso9001entindart`, 
    `splanlar`.`iso9001kalansure`, 
    `splanlar`.`iso9001a1sure`, 
    `splanlar`.`iso9001a2sure`, 
    `splanlar`.`iso9001gsure`,
    `splanlar`.`iso9001ybsure`
FROM `alimentc_easynet`.`splanlar`;