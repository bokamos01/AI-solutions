/*--CLASS DB - DATABASE SCRIPT--*/

/*--AIASSISTANT DATABASE SCRIPT--*/

/*--Drop tables--*/
DROP TABLE IF EXISTS tbl_demonstration;
DROP TABLE IF EXISTS tbl_feedback;
DROP TABLE IF EXISTS tbl_eventregistry;
DROP TABLE IF EXISTS tbl_events;
DROP TABLE IF EXISTS tbl_faq;
DROP TABLE IF EXISTS tbl_staff;
DROP TABLE IF EXISTS tbl_roles;
DROP TABLE IF EXISTS tbl_reports;
DROP TABLE IF EXISTS tbl_countries;

/*Table reports*/
CREATE TABLE tbl_reports(
    REPORTID INT PRIMARY KEY, 
    REPORTNAME VARCHAR(200), 
    CATEGORY VARCHAR(100), 
    PREVIEW VARCHAR(100)
);

/*Table countries*/
CREATE TABLE tbl_countries(
    COUNTRYID   INT NOT NULL PRIMARY KEY,    
    COUNTRY     VARCHAR(100) NOT NULL,
    CAPITALCITY VARCHAR(50)  NOT NULL,
    CONTINENT   VARCHAR(50)  NOT NULL,
    CURRENCY    VARCHAR(20)  NOT NULL,
    COUNTRYCODE VARCHAR(5)   NOT NULL, 
    TELCODE     VARCHAR(10)  NOT NULL
);

/*Table roles*/
CREATE TABLE tbl_roles(
    ROLEID INT PRIMARY KEY NOT NULL,
    ROLE VARCHAR(50) NOT NULL,
    DESCRIPTION VARCHAR(100) NOT NULL
);

/*Table staff*/
CREATE TABLE tbl_staff(
    STAFFID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,       
    FIRSTNAME VARCHAR(30) NOT NULL,
    SURNAME VARCHAR(30) NOT NULL,
    GENDER CHAR(1) NOT NULL,      
    DOB DATE,         
    EMAILADDRESS VARCHAR(100) NOT NULL UNIQUE,
    ROLEID INT NOT NULL,
    COUNTRYID INT NOT NULL,
    DATEREGISTERED DATE,
    FOREIGN KEY (ROLEID) REFERENCES tbl_roles(ROLEID),
    FOREIGN KEY (COUNTRYID) REFERENCES tbl_countries(COUNTRYID)
);

/*Table faq*/
CREATE TABLE tbl_faq(
    FAQID INT PRIMARY KEY, 
    FAQUESTION VARCHAR(50),  
    FAQANSWER VARCHAR(100)
);

/*Table events*/
CREATE TABLE tbl_events(
    EVENTID INT PRIMARY KEY,
    EVENTTITTLE VARCHAR(30), 
    EVENTDESCRIPTION VARCHAR(300) NOT NULL, 
    VANUE VARCHAR(30), 
    EVENTDATE DATE NOT NULL,
    EVENTTIME TIME NOT NULL
);

/*Table event registry*/
CREATE TABLE tbl_eventregistry(
    REGISTRATIONID INT PRIMARY KEY NOT NULL,
    FIRSTNAME VARCHAR(30) NOT NULL,
    SURNAME VARCHAR(30) NOT NULL,       
    EMAILADDRESS VARCHAR(100) NOT NULL UNIQUE,
    PHONENUMBER VARCHAR(30) NOT NULL,
    COMPANYNAME VARCHAR(30) NOT NULL,
    EVENTID INT NOT NULL,
    COUNTRYID INT NOT NULL,
    FOREIGN KEY (EVENTID) REFERENCES tbl_events(EVENTID),
    FOREIGN KEY (COUNTRYID) REFERENCES tbl_countries(COUNTRYID)
);

/*Table feedback*/
CREATE TABLE tbl_feedback(
    FEEDBACKID INT PRIMARY KEY,
    FIRSTNAME VARCHAR(50), 
    LASTNAME VARCHAR(50),  
    EMAILADDRESS VARCHAR(100) UNIQUE,  
    PHONENUMBER VARCHAR(30),
    FEEDBACK VARCHAR(300)
);

/*Table demonstration*/
CREATE TABLE tbl_demonstration(
    DEMONSTRATIONID INT PRIMARY KEY,
    FIRSTNAME VARCHAR(30) NOT NULL,
    LASTNAME VARCHAR(30) NOT NULL,
    EMAILADDRESS VARCHAR(100) NOT NULL,
    PHONENUMBER VARCHAR(30) NOT NULL,
    COMPANYNAME VARCHAR(30) NOT NULL,
    INTERESTDESCRIPTION VARCHAR(300) NOT NULL,
    COUNTRYID INT NOT NULL,
    STAFFID INT NOT NULL,
    DEMOSTATE CHAR(1) NOT NULL,
    FOREIGN KEY (COUNTRYID) REFERENCES tbl_countries(COUNTRYID),
    FOREIGN KEY (STAFFID) REFERENCES tbl_staff(STAFFID)
);

/*table countries*/
INSERT INTO tbl_countries VALUES (1,'Algeria','Algier','Africa','DZD','DZ','00213');
INSERT INTO tbl_countries VALUES (2,'Angola','Luanda','Africa','AOA','AO','00244');
INSERT INTO tbl_countries VALUES (3,'Ascension Island','Georgetown','Africa','AC','AC','00247');
INSERT INTO tbl_countries VALUES (4,'Benin','Porto Novo','Africa','XOF','BJ','00229');
INSERT INTO tbl_countries VALUES (5,'Botswana','Gaborone','Africa','BWP','BW','00267');
INSERT INTO tbl_countries VALUES (6,'Burkina Faso','Ouagadougou','Africa','XOF','BF','00226');
INSERT INTO tbl_countries VALUES (7,'Burundi','Bujumbura','Africa','BIF','BI','00257');
INSERT INTO tbl_countries VALUES (8,'Cameroon','Yaoundé','Africa','XAF','CM','00237');
INSERT INTO tbl_countries VALUES (9,'Cape Verde','Praia','Africa','CVE','CV','00238');
INSERT INTO tbl_countries VALUES (10,'Central African Republic','Bangui','Africa','XAF','CF','00236');
INSERT INTO tbl_countries VALUES (11,'Chad','NDjamena','Africa','XAF','TD','00235');
INSERT INTO tbl_countries VALUES (12,'Comoros','Moroni','Africa','KMF','KM','00269');
INSERT INTO tbl_countries VALUES (13,'Congo','Brazzaville','Africa','XAF','CG','00242');
INSERT INTO tbl_countries VALUES (14,'Congo, The Democratic Republic Of The','Kinshasa','Africa','CDF','CD','00243');
INSERT INTO tbl_countries VALUES (15,'CÔte Divoire','Yamoussoukro','Africa','XOF','CI','00225');
INSERT INTO tbl_countries VALUES (16,'Diego Garcia','Diego Garcia (Main Island)','Africa','DG','DG','00246');
INSERT INTO tbl_countries VALUES (17,'Djibouti','Djibouti City','Africa','DJF','DJ','00253');
INSERT INTO tbl_countries VALUES (18,'Egypt','Cairo','Africa','EGP','EG','0020');
INSERT INTO tbl_countries VALUES (19,'Equatorial Guinea','Malabo','Africa','XAF','GQ','00240');
INSERT INTO tbl_countries VALUES (20,'Eritrea','Asmara (Asmera)','Africa','ERN','ER','00291');
INSERT INTO tbl_countries VALUES (21,'Ethiopia','Addis Abeba','Africa','ETB','ET','00251');
INSERT INTO tbl_countries VALUES (22,'Falkland Islands (malvinas)','Port Stanley','Africa','FLP','FK','00500');
INSERT INTO tbl_countries VALUES (23,'Gabon','Libreville','Africa','XAF','GA','00241');
INSERT INTO tbl_countries VALUES (24,'Ghana','Accra','Africa','GHC','GH','00233');
INSERT INTO tbl_countries VALUES (25,'Gibraltar','Gibraltar (Stadt)','Africa','GIP','GI','00350');
INSERT INTO tbl_countries VALUES (26,'Guinea','Conakry','Africa','GNF','GN','00224');
INSERT INTO tbl_countries VALUES (27,'Guinea-Bissau','Bissau','Africa','XOF','GW','00245');
INSERT INTO tbl_countries VALUES (28,'Kenya','Nairobi','Africa','KES','KE','00254');
INSERT INTO tbl_countries VALUES (29,'Lesotho','Maseru','Africa','LSL','LS','00266');
INSERT INTO tbl_countries VALUES (30,'Liberia','Monrovia','Africa','LRD','LR','00231');
INSERT INTO tbl_countries VALUES (31,'Libya','Tripolis','Africa','LYD','LY','00218');
INSERT INTO tbl_countries VALUES (32,'Madagascar','Antananarivo','Africa','MGA','MG','00261');
INSERT INTO tbl_countries VALUES (33,'Malawi','Lilongwe','Africa','MWK','MW','00265');
INSERT INTO tbl_countries VALUES (34,'Mali','Bamako','Africa','XOF','ML','00223');
INSERT INTO tbl_countries VALUES (35,'Mauritania','Nouakchott','Africa','MRO','MR','00222');
INSERT INTO tbl_countries VALUES (36,'Mauritius','Port Louis','Africa','MUR','MU','00230');
INSERT INTO tbl_countries VALUES (37,'Mayotte','Mamoudzou','Africa','EUR','YT','00269');
INSERT INTO tbl_countries VALUES (38,'Morocco','Rabat','Africa','MAD','MA','00211');
INSERT INTO tbl_countries VALUES (39,'Mozambique','Maputo','Africa','MZM','MZ','00258');
INSERT INTO tbl_countries VALUES (40,'Namibia','Windhoek','Africa','ZAR','NA','00264');
INSERT INTO tbl_countries VALUES (41,'Niger','Niamey','Africa','XOF','NE','00227');
INSERT INTO tbl_countries VALUES (42,'Nigeria','Abuja','Africa','NGN','NG','00234');
INSERT INTO tbl_countries VALUES (43,'RÉunion','Saint-Denis','Africa','EUR','RE','00262');
INSERT INTO tbl_countries VALUES (44,'Rwanda','Kigali','Africa','RWF','RW','00250');
INSERT INTO tbl_countries VALUES (45,'Saint Helena','Jamestown','Africa','SHP','SH','00290');
INSERT INTO tbl_countries VALUES (46,'Sao Tome And Principe','São Tomé','Africa','STD','ST','00239');
INSERT INTO tbl_countries VALUES (47,'Senegal','Dakar','Africa','XOF','SN','00221');
INSERT INTO tbl_countries VALUES (48,'Seychelles','Victoria','Africa','SCR','SC','00248');
INSERT INTO tbl_countries VALUES (49,'Sierra Leone','Freetown','Africa','SLL','SL','00232');
INSERT INTO tbl_countries VALUES (50,'Somalia','Mogadischu','Africa','SOS','SO','00252');
INSERT INTO tbl_countries VALUES (51,'South Africa','Pretoria','Africa','ZAR','ZA','0027');
INSERT INTO tbl_countries VALUES (52,'Sudan','Khartum','Africa','SDD','SD','00249');
INSERT INTO tbl_countries VALUES (53,'Swaziland','Mbabane','Africa','SZL','SZ','00268');
INSERT INTO tbl_countries VALUES (54,'Tanzania, United Republic Of','Dodoma','Africa','TZS','TZ','00255');
INSERT INTO tbl_countries VALUES (55,'The Gambia','Banjul','Africa','GMD','GM','00220');
INSERT INTO tbl_countries VALUES (56,'Togo','Lomé','Africa','XOF','TG','00228');
INSERT INTO tbl_countries VALUES (57,'Tristan da Cunha','Jamestown','Africa','TA','TA','00290');
INSERT INTO tbl_countries VALUES (58,'Tunisia','Tunis','Africa','TND','TN','00216');
INSERT INTO tbl_countries VALUES (59,'Uganda','Kampala','Africa','UGX','UG','00256');
INSERT INTO tbl_countries VALUES (60,'Western Sahara','El Aaiún','Africa','MAD','EH','0000');
INSERT INTO tbl_countries VALUES (61,'Zambia','Lusaka','Africa','ZMK','ZM','00260');
INSERT INTO tbl_countries VALUES (62,'Zimbabwe','Harare','Africa','ZWD','ZW','00263');
INSERT INTO tbl_countries VALUES (63,'Afghanistan','Kabul','Asia','AFN','AF','0093');
INSERT INTO tbl_countries VALUES (64,'Armenia','Eriwan','Asia','AMD','AM','00374');
INSERT INTO tbl_countries VALUES (65,'Azerbaijan','Baku','Asia','AZN','AZ','00994');
INSERT INTO tbl_countries VALUES (66,'Bahrain','Manama','Asia','BHD','BH','00973');
INSERT INTO tbl_countries VALUES (67,'Bangladesh','Dhaka','Asia','BDT','BD','00880');
INSERT INTO tbl_countries VALUES (68,'Bhutan','Thimphu','Asia','BTN','BT','00975');
INSERT INTO tbl_countries VALUES (69,'British Indian Ocean Territory','-/-','Asia','USD','IO','0000');
INSERT INTO tbl_countries VALUES (70,'Brunei Darussalam','Bandar Seri Begawan','Asia','BND','BN','00673');
INSERT INTO tbl_countries VALUES (71,'Burma','Rangun','Asia','MMK','MM','0095');
INSERT INTO tbl_countries VALUES (72,'Cambodia','Phnom Penh','Asia','KHR','KH','00855');
INSERT INTO tbl_countries VALUES (73,'China','Peking (Beijing)','Asia','CNY','CN','0086');
INSERT INTO tbl_countries VALUES (74,'Christmas Island','Flying Fish Cove','Asia','AUD','CX','0061');
INSERT INTO tbl_countries VALUES (75,'Cocos (keeling) Islands','West Island','Asia','AUD','CC','0000');
INSERT INTO tbl_countries VALUES (76,'Cyprus','Nikosia','Asia','CYP','CY','00357');
INSERT INTO tbl_countries VALUES (77,'Guam','Hagåtña','Asia','USD','GU','001671');
INSERT INTO tbl_countries VALUES (78,'Hong Kong','-/-','Asia','HNL','HK','00852');
INSERT INTO tbl_countries VALUES (79,'India','Neu-Delhi','Asia','ISK','IN','0091');
INSERT INTO tbl_countries VALUES (80,'Indonesia','Jakarta','Asia','INR','ID','0062');
INSERT INTO tbl_countries VALUES (81,'Iran','Teheran','Asia','IRR','IR','0098');
INSERT INTO tbl_countries VALUES (82,'Iraq','Bagdad','Asia','IDR','IQ','00964');
INSERT INTO tbl_countries VALUES (83,'Israel','Jerusalem','Asia','ILS','IL','00972');
INSERT INTO tbl_countries VALUES (84,'Japan','Tokio','Asia','JPY','JP','0081');
INSERT INTO tbl_countries VALUES (85,'Jordan','Amman','Asia','JOD','JO','00962');
INSERT INTO tbl_countries VALUES (86,'Kazakhstan','Astana','Asia','KZT','KZ','007');
INSERT INTO tbl_countries VALUES (87,'Kuwait','Kuwait','Asia','KWD','KW','00965');
INSERT INTO tbl_countries VALUES (88,'Kyrgyzstan','Bischkek','Asia','KGS','KG','00996');
INSERT INTO tbl_countries VALUES (89,'Lao Peoples Democratic Republic','Vientiane','Asia','LAK','LA','00856');
INSERT INTO tbl_countries VALUES (90,'Lebanon','Beirut','Asia','LBP','LB','00961');
INSERT INTO tbl_countries VALUES (91,'Macao','-/-','Asia','MOP','MO','00853');
INSERT INTO tbl_countries VALUES (92,'Malaysia','Kuala Lumpur','Asia','MYR','MY','0060');
INSERT INTO tbl_countries VALUES (93,'Maldives','Malé','Asia','MVR','MV','00960');
INSERT INTO tbl_countries VALUES (94,'Mongolia','Ulaanbaatar','Asia','MNT','MN','00976');
INSERT INTO tbl_countries VALUES (95,'Nepal','Kathmandu','Asia','NPR','NP','00977');
INSERT INTO tbl_countries VALUES (96,'North Korea','Pjöngjang','Asia','KPW','KP','00850');
INSERT INTO tbl_countries VALUES (97,'Oman','Maskat','Asia','OMR','OM','00968');
INSERT INTO tbl_countries VALUES (98,'Pakistan','Islamabad','Asia','PKR','PK','0092');
INSERT INTO tbl_countries VALUES (99,'Palestinian Territory, Occupied','Ramallah','Asia','PS','PS','00970');
INSERT INTO tbl_countries VALUES (100,'Philippines','Manila','Asia','PHP','PH','0063');
INSERT INTO tbl_countries VALUES (101,'Qatar','Doha','Asia','QAR','QA','00974');
INSERT INTO tbl_countries VALUES (102,'Russian Federation','Moskau','Asia','RUB','RU','007');
INSERT INTO tbl_countries VALUES (103,'Saudi Arabia','Riad','Asia','SAR','SA','00966');
INSERT INTO tbl_countries VALUES (104,'Saudi–Iraqi neutral zone','-/-','Asia','NT','NT','0000');
INSERT INTO tbl_countries VALUES (105,'Singapore','Singapur','Asia','SGD','SG','0065');
INSERT INTO tbl_countries VALUES (106,'South Korea','Seoul','Asia','KRW','KR','0082');
INSERT INTO tbl_countries VALUES (107,'Sri Lanka','Colombo','Asia','LKR','LK','0094');
INSERT INTO tbl_countries VALUES (108,'Syrian Arab Republic','Damaskus','Asia','SYP','SY','00963');
INSERT INTO tbl_countries VALUES (109,'Taiwan','Taipeh','Asia','TWD','TW','00886');
INSERT INTO tbl_countries VALUES (110,'Tajikistan','Duschanbe','Asia','RUB','TJ','00992');
INSERT INTO tbl_countries VALUES (111,'Thailand','Bangkok','Asia','THB','TH','0066');
INSERT INTO tbl_countries VALUES (112,'Turkey','Ankara','Asia','TRY','TR','0090');
INSERT INTO tbl_countries VALUES (113,'Turkmenistan','Asgabat','Asia','TMM','TM','00993');
INSERT INTO tbl_countries VALUES (114,'United Arab Emirates','Abu Dhabi','Asia','AED','AE','00971');
INSERT INTO tbl_countries VALUES (115,'Uzbekistan','Taschkent','Asia','UZS','UZ','00998');
INSERT INTO tbl_countries VALUES (116,'Viet Nam','Hà N?i','Asia','VND','VN','0084');
INSERT INTO tbl_countries VALUES (117,'Yemen','Sanaa','Asia','YER','YE','00967');
INSERT INTO tbl_countries VALUES (118,'Antarctica','Juneau','Antarctica','AQ','AQ','00672');
INSERT INTO tbl_countries VALUES (119,'Bouvet Island','(Forschungsinsel)','Antarctica','NOK','BV','0000');
INSERT INTO tbl_countries VALUES (120,'French Southern Territories','Port-aux-Français','Antarctica','EUR','TF','0000');
INSERT INTO tbl_countries VALUES (121,'American Samoa','Pago-Pago','Australia','USD','AS','001684');
INSERT INTO tbl_countries VALUES (122,'Australia','Canberra','Australia','AUD','AU','0061');
INSERT INTO tbl_countries VALUES (123,'Cook Islands','Avarua','Australia','NZD','CK','00682');
INSERT INTO tbl_countries VALUES (124,'East Timor','Dili','Australia','IDR','TL','00670');
INSERT INTO tbl_countries VALUES (125,'Fiji','Suva','Australia','FJD','FJ','00679');
INSERT INTO tbl_countries VALUES (126,'French Polynesia','Papeete','Australia','XPF','PF','00689');
INSERT INTO tbl_countries VALUES (127,'Heard Island And Mcdonald Islands','-/-','Australia','AUD','HM','0000');
INSERT INTO tbl_countries VALUES (128,'Kiribati','Bairiki','Australia','AUD','KI','00686');
INSERT INTO tbl_countries VALUES (129,'Marshall Islands','Delap-Uliga-Darrit','Australia','USD','MH','00692');
INSERT INTO tbl_countries VALUES (130,'Micronesia, Federated States Of','Palikir','Australia','USD','FM','00691');
INSERT INTO tbl_countries VALUES (131,'Nauru','Yaren','Australia','AUD','NR','00674');
INSERT INTO tbl_countries VALUES (132,'New Caledonia','Nouméa','Australia','XPF','NC','00687');
INSERT INTO tbl_countries VALUES (133,'New Zealand','Wellington','Australia','NZD','NZ','0064');
INSERT INTO tbl_countries VALUES (134,'Niue','Alofi','Australia','NZD','NU','00683');
INSERT INTO tbl_countries VALUES (135,'Norfolk Island','Kingston','Australia','AUD','NF','006723');
INSERT INTO tbl_countries VALUES (136,'Northern Mariana Islands','Saipan','Australia','USD','MP','001670');
INSERT INTO tbl_countries VALUES (137,'Palau','Melekeok','Australia','USD','PW','00680');
INSERT INTO tbl_countries VALUES (138,'Papua New Guinea','Port Moresby','Australia','PGK','PG','00675');
INSERT INTO tbl_countries VALUES (139,'Pitcairn','Adamstown','Australia','NZD','PN','00649');
INSERT INTO tbl_countries VALUES (140,'Samoa','Apia','Australia','WST','WS','0000');
INSERT INTO tbl_countries VALUES (141,'Solomon Islands','Honiara','Australia','SBD','SB','00677');
INSERT INTO tbl_countries VALUES (142,'Tokelau','-/-','Australia','NZD','TK','00690');
INSERT INTO tbl_countries VALUES (143,'Tonga','Nuku alofa','Australia','TOP','TO','00676');
INSERT INTO tbl_countries VALUES (144,'Tuvalu','Funafuti','Australia','TVD','TV','00688');
INSERT INTO tbl_countries VALUES (145,'Vanuatu','Port Vila','Australia','VUV','VU','00678');
INSERT INTO tbl_countries VALUES (146,'Wallis And Futuna','Mata-Utu','Australia','XPF','WF','00681');
INSERT INTO tbl_countries VALUES (147,'Åland Islands','Mariehamn','Europe','EUR','AX','0035818');
INSERT INTO tbl_countries VALUES (148,'Albania','Tirana','Europe','ALL','AL','00355');
INSERT INTO tbl_countries VALUES (149,'Andorra','Andorra la Vella','Europe','EUR','AD','00376');
INSERT INTO tbl_countries VALUES (150,'Austria','Wien','Europe','EUR','AT','0043');
INSERT INTO tbl_countries VALUES (151,'Belarus','Minsk','Europe','BYR','BY','00375');
INSERT INTO tbl_countries VALUES (152,'Belgium','Brüssel','Europe','EUR','BE','0032');
INSERT INTO tbl_countries VALUES (153,'Bosnia And Herzegovina','Sarajevo','Europe','BAM','BA','00387');
INSERT INTO tbl_countries VALUES (154,'Bulgaria','Sofia','Europe','BGN','BG','00359');
INSERT INTO tbl_countries VALUES (155,'Canary Islands','Santa Cruz de Tenerife','Europe','IC','IC','0000');
INSERT INTO tbl_countries VALUES (156,'Croatia','Zagreb','Europe','HRK','HR','00385');
INSERT INTO tbl_countries VALUES (157,'Czech Republic','Prag','Europe','CZK','CZ','00420');
INSERT INTO tbl_countries VALUES (158,'Denmark','Kopenhagen','Europe','DKK','DK','0045');
INSERT INTO tbl_countries VALUES (159,'Estonia','Tallinn (Reval)','Europe','EEK','EE','00372');
INSERT INTO tbl_countries VALUES (160,'European Union','Brussels','Europe','EUR','EU','003883');
INSERT INTO tbl_countries VALUES (161,'Faroe Islands','Tórshavn','Europe','DKK','FO','00298');
INSERT INTO tbl_countries VALUES (162,'Finland','Helsinki','Europe','EUR','FI','00358');
INSERT INTO tbl_countries VALUES (163,'France','Paris','Europe','EUR','FR','0033');
INSERT INTO tbl_countries VALUES (164,'Georgia','Tiflis','Europe','GEL','GE','00995');
INSERT INTO tbl_countries VALUES (165,'Germany','Berlin','Europe','EUR','DE','0049');
INSERT INTO tbl_countries VALUES (166,'Greece','Athen','Europe','EUR','GR','0030');
INSERT INTO tbl_countries VALUES (167,'Guernsey','St. Peter Port','Europe','GGP','GG','0044148');
INSERT INTO tbl_countries VALUES (168,'Hungary','Budapest','Europe','HUF','HU','0036');
INSERT INTO tbl_countries VALUES (169,'Iceland','Reykjavík','Europe','HUF','IS','00354');
INSERT INTO tbl_countries VALUES (170,'Ireland','Dublin','Europe','EUR','IE','00353');
INSERT INTO tbl_countries VALUES (171,'Isle Of Man','Douglas','Europe','IMP','IM','0044');
INSERT INTO tbl_countries VALUES (172,'Italy','Rom','Europe','EUR','IT','0039');
INSERT INTO tbl_countries VALUES (173,'Jersey','Saint Helier','Europe','JEP','JE','0044');
INSERT INTO tbl_countries VALUES (174,'Latvia','Riga','Europe','LVL','LV','00371');
INSERT INTO tbl_countries VALUES (175,'Liechtenstein','Vaduz','Europe','CHF','LI','00423');
INSERT INTO tbl_countries VALUES (176,'Lithuania','Wilna','Europe','LTL','LT','00370');
INSERT INTO tbl_countries VALUES (177,'Luxembourg','Luxemburg','Europe','EUR','LU','00352');
INSERT INTO tbl_countries VALUES (178,'Malta','Valletta','Europe','EUR','MT','00356');
INSERT INTO tbl_countries VALUES (179,'Moldova','Chisinau','Europe','MDL','MD','00373');
INSERT INTO tbl_countries VALUES (180,'Monaco','Monaco','Europe','EUR','MC','00377');
INSERT INTO tbl_countries VALUES (181,'Montenegro','Podgorica','Europe','ME','ME','00382');
INSERT INTO tbl_countries VALUES (182,'Netherlands','Amsterdam','Europe','EUR','NL','0031');
INSERT INTO tbl_countries VALUES (183,'Norway','Oslo','Europe','NOK','NO','0047');
INSERT INTO tbl_countries VALUES (184,'Poland','Warschau','Europe','PLN','PL','0048');
INSERT INTO tbl_countries VALUES (185,'Portugal','Lissabon','Europe','EUR','PT','00351');
INSERT INTO tbl_countries VALUES (186,'Republic of Macedonia','Skopje','Europe','MKD','MK','00389');
INSERT INTO tbl_countries VALUES (187,'Romania','Bukarest','Europe','RON','RO','0040');
INSERT INTO tbl_countries VALUES (188,'San Marino','San Marino','Europe','EUR','SM','00378');
INSERT INTO tbl_countries VALUES (189,'Serbia','Belgrade','Europe','RSD','RS','00381');
INSERT INTO tbl_countries VALUES (190,'Serbien und Montenegro','Belgrad','Europe','CS','CS','00381');
INSERT INTO tbl_countries VALUES (191,'Slovakia','Bratislava','Europe','SKK','SK','00421');
INSERT INTO tbl_countries VALUES (192,'Slovenia','Ljubljana','Europe','SIT','SI','00386');
INSERT INTO tbl_countries VALUES (193,'Soviet Union','Moskau','Europe','SU','SU','0000');
INSERT INTO tbl_countries VALUES (194,'Spain','Madrid','Europe','EUR','ES','0034');
INSERT INTO tbl_countries VALUES (195,'Svalbard And Jan Mayen','Longyearbyen','Europe','NOK','SJ','0000');
INSERT INTO tbl_countries VALUES (196,'Sweden','Stockholm','Europe','SEK','SE','0046');
INSERT INTO tbl_countries VALUES (197,'Switzerland','Bern','Europe','CHF','CH','0041');
INSERT INTO tbl_countries VALUES (198,'Ukraine','Kiew','Europe','UAH','UA','00380');
INSERT INTO tbl_countries VALUES (199,'United Kingdom','London','Europe','GBP','GB','0044');
INSERT INTO tbl_countries VALUES (200,'Vatican City','Vatican City','Europe','EUR','VA','003906');
INSERT INTO tbl_countries VALUES (201,'Anguilla','The Valley','North America','XCD','AI','001264');
INSERT INTO tbl_countries VALUES (202,'Antigua And Barbuda','Saint Johns','North America','XCD','AG','001268');
INSERT INTO tbl_countries VALUES (203,'Aruba','Oranjestad','North America','ANG','AW','00297');
INSERT INTO tbl_countries VALUES (204,'Bahamas','Nassau','North America','BSD','BS','001242');
INSERT INTO tbl_countries VALUES (205,'Barbados','Bridgetown','North America','BBD','BB','001246');
INSERT INTO tbl_countries VALUES (206,'Belize','Belmopan','North America','BZD','BZ','0051');
INSERT INTO tbl_countries VALUES (207,'Bermuda','Hamilton','North America','BMD','BM','001441');
INSERT INTO tbl_countries VALUES (208,'British Virgin Islands','Road Town','North America','USD','VG','001284');
INSERT INTO tbl_countries VALUES (209,'Canada','Ottawa','North America','CAD','CA','0000');
INSERT INTO tbl_countries VALUES (210,'Cayman Islands','George Town','North America','KYD','KY','001345');
INSERT INTO tbl_countries VALUES (211,'Costa Rica','San José','North America','CRC','CR','0056');
INSERT INTO tbl_countries VALUES (212,'Cuba','Havanna','North America','CUP','CU','0053');
INSERT INTO tbl_countries VALUES (213,'Dominica','Roseau','North America','XCD','DM','001767');
INSERT INTO tbl_countries VALUES (214,'El Salvador','San Salvador','North America','SVC','SV','0053');
INSERT INTO tbl_countries VALUES (215,'Greenland','Nuuk','North America','DKK','GL','00299');
INSERT INTO tbl_countries VALUES (216,'Grenada','St. Georges','North America','XCD','GD','001473');
INSERT INTO tbl_countries VALUES (217,'Guadeloupe','Basse-Terre','North America','EUR','GP','00590');
INSERT INTO tbl_countries VALUES (218,'Guatemala','Guatemala-Stadt','North America','GTQ','GT','0052');
INSERT INTO tbl_countries VALUES (219,'Haiti','Port-au-Prince','North America','USD','HT','0059');
INSERT INTO tbl_countries VALUES (220,'Honduras','Tegucigalpa','North America','HNL','HN','0054');
INSERT INTO tbl_countries VALUES (221,'Jamaica','Kingston','North America','JMD','JM','001876');
INSERT INTO tbl_countries VALUES (222,'Martinique','Fort-de-France','North America','EUR','MQ','00596');
INSERT INTO tbl_countries VALUES (223,'Mexico','Mexiko-Stadt','North America','MXN','MX','0052');
INSERT INTO tbl_countries VALUES (224,'Montserrat','Plymouth','North America','XCD','MS','001664');
INSERT INTO tbl_countries VALUES (225,'Netherlands Antilles','Willemstad','North America','ANG','AN','00599');
INSERT INTO tbl_countries VALUES (226,'Nicaragua','Managua','North America','NIO','NI','0055');
INSERT INTO tbl_countries VALUES (227,'Puerto Rico','San Juan','North America','USD','PR','001939');
INSERT INTO tbl_countries VALUES (228,'Saint Kitts And Nevis','Basseterre','North America','XCD','KN','001869');
INSERT INTO tbl_countries VALUES (229,'Saint Pierre And Miquelon','Saint-Pierre','North America','EUR','PM','00508');
INSERT INTO tbl_countries VALUES (230,'Turks And Caicos Islands','Cockburn Town auf Grand Turk','North America','USD','TC','001649');
INSERT INTO tbl_countries VALUES (231,'United States','Washington, D.C.','North America','USD','US','001');
INSERT INTO tbl_countries VALUES (232,'Argentina','Buenos Aires','South America','ARS','AR','0054');
INSERT INTO tbl_countries VALUES (233,'Bolivia','Sucre','South America','BOB','BO','00591');
INSERT INTO tbl_countries VALUES (234,'Brazil','Brasília','South America','BRL','BR','0055');
INSERT INTO tbl_countries VALUES (235,'Chile','Santiago','South America','CLP','CL','0056');
INSERT INTO tbl_countries VALUES (236,'Colombia','Santa Fé de Bogotá','South America','COP','CO','0057');
INSERT INTO tbl_countries VALUES (237,'Dominican Republic','Santo Domingo','South America','DOP','DO','001809');
INSERT INTO tbl_countries VALUES (238,'Ecuador','Quito','South America','USD','EC','00593');
INSERT INTO tbl_countries VALUES (239,'French Guiana','Cayenne','South America','EUR','GF','00594');
INSERT INTO tbl_countries VALUES (240,'Guyana','Georgetown','South America','GYD','GY','00592');
INSERT INTO tbl_countries VALUES (241,'Panama','Panama City','South America','USD','PA','0057');
INSERT INTO tbl_countries VALUES (242,'Paraguay','Asunción','South America','PYG','PY','00595');
INSERT INTO tbl_countries VALUES (243,'Peru','Lima','South America','PEN','PE','0051');
INSERT INTO tbl_countries VALUES (244,'Saint Lucia','Castries','South America','XCD','LC','001758');
INSERT INTO tbl_countries VALUES (245,'Saint Vincent and the Grenadines','Kingstown','South America','XCD','VC','001784');
INSERT INTO tbl_countries VALUES (246,'South Georgia And The South Sandwich Islands','King Edward Point','South America','GBP','GS','0000');
INSERT INTO tbl_countries VALUES (247,'Suriname','Paramaribo','South America','SRD','SR','00597');
INSERT INTO tbl_countries VALUES (248,'Trinidad And Tobago','Port-of-Spain','South America','TTD','TT','001868');
INSERT INTO tbl_countries VALUES (249,'Uruguay','Montevideo','South America','UYU','UY','00598');
INSERT INTO tbl_countries VALUES (250,'Venezuela','Caracas','South America','VEB','VE','0058');
INSERT INTO tbl_countries VALUES (251,'Virgin Islands, U.s.','Charlotte Amalie','South America','USD','VI','001340');

/*table roles*/
INSERT INTO tbl_roles VALUES (1,'Administrator','All members who are administrators');
INSERT INTO tbl_roles VALUES (2,'Demonstator','All members who are demonstrators');

/*table staff*/
INSERT INTO tbl_staff VALUES (1, 'Luke','Skywalker','M','1999-01-01','skywalker@example.com',1,5,'2025-01-01');