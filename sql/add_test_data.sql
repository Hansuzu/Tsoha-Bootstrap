-- Lisää INSERT INTO lauseet tähän tiedostoon

INSERT INTO Person (username,email,pword,is_admin,is_moderator,edit_allowed,messages_allowed,created) VALUES ('lol','lol@lol','1234',b'1',b'1',b'1',b'1',NOW());
INSERT INTO AbstractArticle DEFAULT VALUES;
INSERT INTO AbstractArticle DEFAULT VALUES;
INSERT INTO Language (name) VALUES ('Abbagabba');
INSERT INTO Article (abstract_id,language_id,readonly,name) VALUES (1,1,b'1','diipadaapa');
INSERT INTO Article (abstract_id,language_id,readonly,name) VALUES (2,1,b'1','diipadaapa2');
INSERT INTO ArticleVersion (article_id,parent_id,user_id,time,contents) VALUES (1,NULL,1,NOW(),'tietoa');
INSERT INTO ArticleSuperClass (suparticle_id,subarticle_id) VALUES (1,2);
INSERT INTO Message (article_id,user_id,message,time,edited) VALUES (1,1,'viestini',NOW(),NOW());



