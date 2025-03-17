truncate table `alimentc_nazdys`.`plan_27001`;
INSERT INTO `alimentc_nazdys`.`plan_27001` (
    `plan_27001`.`planno`,
    `plan_27001`.`iso27001hamsure`, 
    `plan_27001`.`iso27001indart`, 
    `plan_27001`.`iso27001indartsure`, 
    `plan_27001`.`iso27001entindart`, 
    `plan_27001`.`iso27001kalansure`, 
    `plan_27001`.`iso27001a1sure`, 
    `plan_27001`.`iso27001a2sure`, 
    `plan_27001`.`iso27001gsure`,
    `plan_27001`.`iso27001ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso27001hamsure`, 
    `splanlar`.`iso27001indart`, 
    `splanlar`.`iso27001normalsure`, 
    `splanlar`.`iso27001entindart`, 
    `splanlar`.`iso27001kalansure`, 
    `splanlar`.`iso27001a1sure`, 
    `splanlar`.`iso27001a2sure`, 
    `splanlar`.`iso27001gsure`,
    `splanlar`.`iso27001ybsure`
FROM `alimentc_easynet`.`splanlar`;