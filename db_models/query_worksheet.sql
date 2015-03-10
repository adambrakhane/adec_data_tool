select * from communities;
select * from circuit_riders;
select * from questions;
select * from data ORDER BY recorded_date DESC;
delete from data where create_time>0;
select * from community_gps;
select * from community_junta;
select * from `data` WHERE community_id=21 GROUP BY `question_id` ORDER BY `recorded_date` DESC;
delete from `data` where community_id=21 AND question_id=2;
UPDATE `adeccat`.`circuit_riders` SET `password`=MD5('peppy1') WHERE `id`='2';
INSERT INTO `community_gps` (`community_id`, `location_name`, `gps_lat`, `gps_lon`, `gps_ele`, `comments`) VALUES (13, 'loc2', 12.5, 12.4, 00, 'hi') ON DUPLICATE KEY UPDATE `gps_lat`=VALUES(`gps_lat`), `gps_lon`=VALUES(`gps_lon`), `gps_ele`=VALUES(`gps_ele`), `comments`=VALUES(`comments`);
select * from community_gps;
DELETE FROM `community_gps` WHERE `community_id`=13 AND `location_name` IN ('loc2', 'loc', 'test');
delete from communities where `community`='' AND id>0 AND department='' and municipality='';


SELECT `data`.* FROM ( SELECT *, MAX(`recorded_date`) as test FROM `data` ORDER BY `data`.recorded_date DESC ) AS `data`
WHERE `data`.community_id=21 AND `data`.recorded_date = test
ORDER BY `data`.question_id ASC;


/*Get the latest eval data*/
SELECT `data`.* FROM 
(
    SELECT * 
    FROM `data`
    ORDER BY `data`.recorded_date DESC
) AS `data`
WHERE `data`.community_id=21
GROUP BY `data`.question_id
ORDER BY `data`.question_id ASC;