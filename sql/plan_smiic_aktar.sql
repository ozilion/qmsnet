truncate table `alimentc_nazdys`.`plan_smiic1`;
INSERT INTO `alimentc_nazdys`.`plan_smiic1` (
    `plan_smiic1`.`planno`,
    `plan_smiic1`.`oicsmiichamsure`, 
    `plan_smiic1`.`oicsmiicindart`, 
    `plan_smiic1`.`oicsmiicindartsure`, 
    `plan_smiic1`.`oicsmiicentindart`, 
    `plan_smiic1`.`oicsmiickalansure`, 
    `plan_smiic1`.`oicsmiica1sure`, 
    `plan_smiic1`.`oicsmiica2sure`, 
    `plan_smiic1`.`oicsmiicgsure`,
    `plan_smiic1`.`oicsmiicybsure`)          
SELECT `splanlar`.`planno`,
    `splanlar`.`oicsmiicnormalsure`, 
    `splanlar`.`oicsmiicindart`, 
    `splanlar`.`oicsmiicnormalsure`, 
    `splanlar`.`oicsmiicentindart`, 
    `splanlar`.`oicsmiickalansure`, 
    `splanlar`.`oicsmiica1sure`, 
    `splanlar`.`oicsmiica2sure`, 
    `splanlar`.`oicsmiicgsure`,
    `splanlar`.`oicsmiicybsure`
FROM `alimentc_easynet`.`splanlar`;