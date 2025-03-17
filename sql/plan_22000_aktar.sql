truncate table `alimentc_nazdys`.`plan_22000`;
INSERT INTO `alimentc_nazdys`.`plan_22000` (
    `plan_22000`.`planno`,
    `plan_22000`.`iso22000hamsure`, 
    `plan_22000`.`iso22000indart`, 
    `plan_22000`.`iso22000indartsure`, 
    `plan_22000`.`iso22000entindart`, 
    `plan_22000`.`iso22000kalansure`, 
    `plan_22000`.`iso22000a1sure`, 
    `plan_22000`.`iso22000a2sure`, 
    `plan_22000`.`iso22000gsure`,
    `plan_22000`.`iso22000ybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`iso22000normalsure`, 
    `splanlar`.`iso22000indart`, 
    `splanlar`.`iso22000normalsure`, 
    `splanlar`.`iso22000entindart`, 
    `splanlar`.`iso22000kalansure`, 
    `splanlar`.`iso22000a1sure`, 
    `splanlar`.`iso22000a2sure`, 
    `splanlar`.`iso22000gsure`,
    `splanlar`.`iso22000ybsure`
FROM `alimentc_easynet`.`splanlar`;