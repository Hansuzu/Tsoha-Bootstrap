-- Lisää INSERT INTO lauseet tähän tiedostoon

INSERT INTO Person (username,email,pword,is_admin,is_moderator,edit_allowed,messages_allowed,created) VALUES ('root','','$2y$10$.Ya/N/wHQo8O2RuEGw0cPu9JrToiifRD9Hahg8zKFwEdSvDI35Sae',b'1',b'1',b'1',b'1',NOW());
INSERT INTO Person (username,email,pword,is_admin,is_moderator,edit_allowed,messages_allowed,created) VALUES ('moderator','','$2y$10$.limhft9daF6MKdLkBVwsu6oprP1eWOasXxRknGjYKL5m9BaZsD1K',b'0',b'1',b'1',b'1',NOW());
INSERT INTO Person (username,email,pword,is_admin,is_moderator,edit_allowed,messages_allowed,created) VALUES ('user','','$2y$10$5QGrEGLvtmw3Cr5H5f0qnuXT/Y0govffvuHi26qTJulJOcj8884vW',b'0',b'0',b'1',b'1',NOW());
INSERT INTO AbstractArticle DEFAULT VALUES;
INSERT INTO AbstractArticle DEFAULT VALUES;
INSERT INTO Language (name, shortcode) VALUES ('Abbagabba', 'abb');
INSERT INTO Language (name, shortcode) VALUES ('Suomi', 'fin');
INSERT INTO Article (abstract_id,language_id,readonly,name) VALUES (1,1,b'0','diipadaapa');
INSERT INTO Article (abstract_id,language_id,readonly,name) VALUES (1,2,b'0','diipadaapa Suomeksi');
INSERT INTO Article (abstract_id,language_id,readonly,name) VALUES (2,1,b'1','diipadaapa2');
INSERT INTO ArticleVersion (article_id,parent_id,user_id,time,active,contents) VALUES (1,NULL,1,NOW(),b'1','Dippa dappa diipa daaba abba gabba.');
INSERT INTO ArticleVersion (article_id,parent_id,user_id,time,active,contents) VALUES (2,NULL,1,NOW(),b'1','Perin juurin hyödyllistä ja peräti tärkeätä tietoa aiheesta diipadaapa.');
INSERT INTO ArticleVersion (article_id,parent_id,user_id,time,active,contents) VALUES (3,NULL,1,NOW(),b'1','Dippadappa2');
INSERT INTO ArticleSuperClass (suparticle_id,subarticle_id) VALUES (1,3);
INSERT INTO Message (article_id,user_id,message,time,edited) VALUES (1,1,'viestini',NOW(),NOW());



