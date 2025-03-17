UPDATE `sertifikalar` SET planno=0 where planno is null;
UPDATE `sertifikalar` SET planno=0 where planno="";
UPDATE `sertifikalar` SET ilkyayin=ilkyayintarihi where ilkyayin is null;
UPDATE `sertifikalar` SET ilkyayin=ilkyayintarihi where ilkyayin="";
UPDATE `sertifikalar` SET bittarihi=bitistarihi where bittarihi is null;
UPDATE `sertifikalar` SET bittarihi=bitistarihi where bittarihi="";