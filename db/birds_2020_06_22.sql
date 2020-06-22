UPDATE `animal_category_master` SET `acm_url_name`='All' WHERE `acm_id`='1';
UPDATE `animal_category_master` SET `acm_url_name`='Bird' WHERE `acm_id`='2';

UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='1';
UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='2';
UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='3';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='4';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='5';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='6';

UPDATE animal_category_master SET parent_id = 2 WHERE parent_id=1;




