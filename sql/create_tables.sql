-- Lisää CREATE TABLE lauseet tähän tiedostoon

CREATE TABLE Person(
    id SERIAL PRIMARY KEY NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    pword VARCHAR(50) NOT NULL,
    is_admin BIT(1) NOT NULL,
    is_moderator BIT(1) NOT NULL,
    edit_allowed BIT(1) NOT NULL,
    messages_allowed BIT(1) NOT NULL,
    created TIMESTAMP NOT NULL
);
CREATE TABLE AbstractArticle(
    id SERIAL PRIMARY KEY NOT NULL
);

CREATE TABLE Language(
    id SERIAL PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE Article(
    id SERIAL PRIMARY KEY NOT NULL,
    abstract_id INT REFERENCES AbstractArticle(id) NOT NULL,
    language_id INT REFERENCES Language(id) NOT NULL,
    readonly BIT(1) NOT NULL,
    name VARCHAR(50) NOT NULL
);
CREATE TABLE ArticleVersion(
    id SERIAL PRIMARY KEY NOT NULL,
    article_id INT REFERENCES Article(id) NOT NULL,
    parent_id INT REFERENCES ArticleVersion(id),
    user_id INT REFERENCES Person(id) NOT NULL,
    time TIMESTAMP NOT NULL,
    contents TEXT NOT NULL
);
CREATE TABLE ArticleSuperclass(
    subarticle_id INT REFERENCES Article(id) NOT NULL,
    suparticle_id INT REFERENCES Article(id) NOT NULL
);
CREATE TABLE Message(
    id SERIAL PRIMARY KEY NOT NULL,
    article_id INT REFERENCES Article(id) NOT NULL,
    user_id INT REFERENCES Person(id) NOT NULL,
    message TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    edited TIMESTAMP NOT NULL
);
