--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

ALTER TABLE ONLY public.movie_role DROP CONSTRAINT movie_role_role_id_fkey;
ALTER TABLE ONLY public.movie_role DROP CONSTRAINT movie_role_person_id_fkey;
ALTER TABLE ONLY public.movie_role DROP CONSTRAINT movie_role_movie_id_fkey;
ALTER TABLE ONLY public.movie DROP CONSTRAINT movie_media_id_fkey;
ALTER TABLE ONLY public.movie_keyword DROP CONSTRAINT movie_keyword_movie_id_fkey;
ALTER TABLE ONLY public.movie_keyword DROP CONSTRAINT movie_keyword_keyword_id_fkey;
ALTER TABLE ONLY public.movie_genre DROP CONSTRAINT movie_genre_movie_id_fkey;
ALTER TABLE ONLY public.movie_genre DROP CONSTRAINT movie_genre_genre_id_fkey;
ALTER TABLE ONLY public.movie DROP CONSTRAINT movie_certificate_id_fkey;
ALTER TABLE ONLY public.media DROP CONSTRAINT media_media_storage_id_fkey;
ALTER TABLE ONLY public.media DROP CONSTRAINT media_media_region_id_fkey;
ALTER TABLE ONLY public.media DROP CONSTRAINT media_media_format_id_fkey;
DROP INDEX public.person_idx;
DROP INDEX public.movie_role_person_idx;
DROP INDEX public.keyword_movie_idx;
DROP INDEX public.keyword_idx;
DROP INDEX public.certificate_idx;
ALTER TABLE ONLY public.role DROP CONSTRAINT role_pkey;
ALTER TABLE ONLY public.person DROP CONSTRAINT person_pkey;
ALTER TABLE ONLY public.movie_role DROP CONSTRAINT movie_role_movie_id_key;
ALTER TABLE ONLY public.movie DROP CONSTRAINT movie_pkey;
ALTER TABLE ONLY public.movie_keyword DROP CONSTRAINT mk;
ALTER TABLE ONLY public.movie_genre DROP CONSTRAINT mg;
ALTER TABLE ONLY public.media_storage DROP CONSTRAINT media_storage_pkey;
ALTER TABLE ONLY public.media_region DROP CONSTRAINT media_region_pkey;
ALTER TABLE ONLY public.media DROP CONSTRAINT media_pkey;
ALTER TABLE ONLY public.media_format DROP CONSTRAINT media_format_pkey;
ALTER TABLE ONLY public.keyword DROP CONSTRAINT keyword_pkey;
ALTER TABLE ONLY public.movie DROP CONSTRAINT imdb;
ALTER TABLE ONLY public.genre DROP CONSTRAINT genre_pkey;
ALTER TABLE ONLY public.genre DROP CONSTRAINT genre_genre_key;
ALTER TABLE ONLY public.certificate DROP CONSTRAINT certificate_pkey;
DROP TABLE public.role;
DROP SEQUENCE public.role_id;
DROP TABLE public.person;
DROP SEQUENCE public.person_id;
DROP TABLE public.movie_role;
DROP TABLE public.movie_keyword;
DROP TABLE public.movie_genre;
DROP TABLE public.movie;
DROP SEQUENCE public.movie_id;
DROP TABLE public.media_storage;
DROP TABLE public.media_region;
DROP TABLE public.media_format;
DROP TABLE public.media;
DROP SEQUENCE public.media_id;
DROP TABLE public.keyword;
DROP SEQUENCE public.keyword_id;
DROP TABLE public.genre;
DROP SEQUENCE public.genre_id;
DROP TABLE public.certificate;
DROP EXTENSION tablefunc;
DROP EXTENSION plpgsql;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: tablefunc; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS tablefunc WITH SCHEMA public;


--
-- Name: EXTENSION tablefunc; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION tablefunc IS 'functions that manipulate whole tables, including crosstab';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: certificate; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE certificate (
    certificate_id smallint NOT NULL,
    certificate character varying(3) NOT NULL,
    "order" smallint NOT NULL
);


ALTER TABLE public.certificate OWNER TO movies;

--
-- Name: genre_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE genre_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.genre_id OWNER TO movies;

--
-- Name: genre; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE genre (
    genre_id smallint DEFAULT nextval('genre_id'::regclass) NOT NULL,
    genre text NOT NULL
);


ALTER TABLE public.genre OWNER TO movies;

--
-- Name: keyword_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE keyword_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.keyword_id OWNER TO movies;

--
-- Name: keyword; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE keyword (
    keyword_id smallint DEFAULT nextval('keyword_id'::regclass) NOT NULL,
    keyword text NOT NULL
);


ALTER TABLE public.keyword OWNER TO movies;

--
-- Name: media_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE media_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.media_id OWNER TO movies;

--
-- Name: media; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE media (
    media_id integer DEFAULT nextval('media_id'::regclass) NOT NULL,
    media_format_id smallint NOT NULL,
    media_region_id smallint NOT NULL,
    media_storage_id smallint NOT NULL,
    amazon_asin character varying(10) NOT NULL,
    purchase_price numeric(5,2),
    current_price numeric(5,2),
    special_edition boolean NOT NULL,
    boxset boolean NOT NULL,
    notes text
);


ALTER TABLE public.media OWNER TO movies;

--
-- Name: media_format; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE media_format (
    media_format_id smallint NOT NULL,
    media_format character varying(7) NOT NULL
);


ALTER TABLE public.media_format OWNER TO movies;

--
-- Name: media_region; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE media_region (
    media_region_id smallint NOT NULL,
    region character varying(4) NOT NULL
);


ALTER TABLE public.media_region OWNER TO movies;

--
-- Name: media_storage; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE media_storage (
    media_storage_id smallint NOT NULL,
    media_storage character(1) NOT NULL
);


ALTER TABLE public.media_storage OWNER TO movies;

--
-- Name: movie_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE movie_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.movie_id OWNER TO movies;

--
-- Name: movie; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE movie (
    movie_id smallint DEFAULT nextval('movie_id'::regclass) NOT NULL,
    imdb_id character varying(10) NOT NULL,
    title text NOT NULL,
    path text NOT NULL,
    filesize bigint NOT NULL,
    deleted boolean DEFAULT false NOT NULL,
    date_added date DEFAULT now() NOT NULL,
    date_last_scanned date DEFAULT now() NOT NULL,
    date_last_scraped date,
    imdb_rating numeric(2,1),
    runtime smallint,
    synopsis text,
    release_year smallint,
    watched boolean DEFAULT false NOT NULL,
    has_image boolean DEFAULT false NOT NULL,
    hd boolean DEFAULT false NOT NULL,
    certificate_id smallint,
    media_id smallint
);


ALTER TABLE public.movie OWNER TO movies;

--
-- Name: movie_genre; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE movie_genre (
    movie_id smallint NOT NULL,
    genre_id smallint NOT NULL
);


ALTER TABLE public.movie_genre OWNER TO movies;

--
-- Name: movie_keyword; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE movie_keyword (
    movie_id smallint NOT NULL,
    keyword_id smallint NOT NULL
);


ALTER TABLE public.movie_keyword OWNER TO movies;

--
-- Name: movie_role; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE movie_role (
    movie_id smallint NOT NULL,
    role_id smallint NOT NULL,
    person_id smallint NOT NULL
);


ALTER TABLE public.movie_role OWNER TO movies;

--
-- Name: person_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE person_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.person_id OWNER TO movies;

--
-- Name: person; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE person (
    person_id smallint DEFAULT nextval('person_id'::regclass) NOT NULL,
    person_name text NOT NULL
);


ALTER TABLE public.person OWNER TO movies;

--
-- Name: role_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE role_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.role_id OWNER TO movies;

--
-- Name: role; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE role (
    role_id smallint DEFAULT nextval('role_id'::regclass) NOT NULL,
    role character varying(8) NOT NULL
);


ALTER TABLE public.role OWNER TO movies;

--
-- Name: certificate_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY certificate
    ADD CONSTRAINT certificate_pkey PRIMARY KEY (certificate_id);


--
-- Name: genre_genre_key; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY genre
    ADD CONSTRAINT genre_genre_key UNIQUE (genre);


--
-- Name: genre_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY genre
    ADD CONSTRAINT genre_pkey PRIMARY KEY (genre_id);


--
-- Name: imdb; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY movie
    ADD CONSTRAINT imdb UNIQUE (imdb_id);


--
-- Name: keyword_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY keyword
    ADD CONSTRAINT keyword_pkey PRIMARY KEY (keyword_id);


--
-- Name: media_format_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY media_format
    ADD CONSTRAINT media_format_pkey PRIMARY KEY (media_format_id);


--
-- Name: media_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY media
    ADD CONSTRAINT media_pkey PRIMARY KEY (media_id);


--
-- Name: media_region_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY media_region
    ADD CONSTRAINT media_region_pkey PRIMARY KEY (media_region_id);


--
-- Name: media_storage_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY media_storage
    ADD CONSTRAINT media_storage_pkey PRIMARY KEY (media_storage_id);


--
-- Name: mg; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY movie_genre
    ADD CONSTRAINT mg UNIQUE (movie_id, genre_id);


--
-- Name: mk; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY movie_keyword
    ADD CONSTRAINT mk UNIQUE (movie_id, keyword_id);


--
-- Name: movie_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY movie
    ADD CONSTRAINT movie_pkey PRIMARY KEY (movie_id);


--
-- Name: movie_role_movie_id_key; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY movie_role
    ADD CONSTRAINT movie_role_movie_id_key UNIQUE (movie_id, role_id, person_id);


--
-- Name: person_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY person
    ADD CONSTRAINT person_pkey PRIMARY KEY (person_id);


--
-- Name: role_pkey; Type: CONSTRAINT; Schema: public; Owner: movies; Tablespace: 
--

ALTER TABLE ONLY role
    ADD CONSTRAINT role_pkey PRIMARY KEY (role_id);


--
-- Name: certificate_idx; Type: INDEX; Schema: public; Owner: movies; Tablespace: 
--

CREATE INDEX certificate_idx ON movie USING btree (certificate_id);


--
-- Name: keyword_idx; Type: INDEX; Schema: public; Owner: movies; Tablespace: 
--

CREATE UNIQUE INDEX keyword_idx ON keyword USING btree (keyword);

ALTER TABLE keyword CLUSTER ON keyword_idx;


--
-- Name: keyword_movie_idx; Type: INDEX; Schema: public; Owner: movies; Tablespace: 
--

CREATE UNIQUE INDEX keyword_movie_idx ON movie_keyword USING btree (movie_id, keyword_id);


--
-- Name: movie_role_person_idx; Type: INDEX; Schema: public; Owner: movies; Tablespace: 
--

CREATE UNIQUE INDEX movie_role_person_idx ON movie_role USING btree (movie_id, role_id, person_id);


--
-- Name: person_idx; Type: INDEX; Schema: public; Owner: movies; Tablespace: 
--

CREATE UNIQUE INDEX person_idx ON person USING btree (person_name);


--
-- Name: media_media_format_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY media
    ADD CONSTRAINT media_media_format_id_fkey FOREIGN KEY (media_format_id) REFERENCES media_format(media_format_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: media_media_region_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY media
    ADD CONSTRAINT media_media_region_id_fkey FOREIGN KEY (media_region_id) REFERENCES media_region(media_region_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: media_media_storage_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY media
    ADD CONSTRAINT media_media_storage_id_fkey FOREIGN KEY (media_storage_id) REFERENCES media_storage(media_storage_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_certificate_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie
    ADD CONSTRAINT movie_certificate_id_fkey FOREIGN KEY (certificate_id) REFERENCES certificate(certificate_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_genre_genre_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_genre
    ADD CONSTRAINT movie_genre_genre_id_fkey FOREIGN KEY (genre_id) REFERENCES genre(genre_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_genre_movie_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_genre
    ADD CONSTRAINT movie_genre_movie_id_fkey FOREIGN KEY (movie_id) REFERENCES movie(movie_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_keyword_keyword_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_keyword
    ADD CONSTRAINT movie_keyword_keyword_id_fkey FOREIGN KEY (keyword_id) REFERENCES keyword(keyword_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_keyword_movie_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_keyword
    ADD CONSTRAINT movie_keyword_movie_id_fkey FOREIGN KEY (movie_id) REFERENCES movie(movie_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_media_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie
    ADD CONSTRAINT movie_media_id_fkey FOREIGN KEY (media_id) REFERENCES media(media_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_role_movie_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_role
    ADD CONSTRAINT movie_role_movie_id_fkey FOREIGN KEY (movie_id) REFERENCES movie(movie_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_role_person_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_role
    ADD CONSTRAINT movie_role_person_id_fkey FOREIGN KEY (person_id) REFERENCES person(person_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: movie_role_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: movies
--

ALTER TABLE ONLY movie_role
    ADD CONSTRAINT movie_role_role_id_fkey FOREIGN KEY (role_id) REFERENCES role(role_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

