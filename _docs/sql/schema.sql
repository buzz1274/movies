--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: certificate_id; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE certificate_id
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.certificate_id OWNER TO movies;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: certificate; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE certificate (
    certificate_id smallint DEFAULT nextval('certificate_id'::regclass) NOT NULL,
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
    keyword_id integer DEFAULT nextval('keyword_id'::regclass) NOT NULL,
    keyword text NOT NULL
);


ALTER TABLE public.keyword OWNER TO movies;

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
    width smallint DEFAULT 0 NOT NULL,
    height smallint DEFAULT 0 NOT NULL
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
    keyword_id integer NOT NULL,
    "order" smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.movie_keyword OWNER TO movies;

--
-- Name: movie_role; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE movie_role (
    movie_id bigint NOT NULL,
    role_id smallint NOT NULL,
    person_id bigint NOT NULL,
    "order" smallint DEFAULT 0 NOT NULL
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
    person_id bigint DEFAULT nextval('person_id'::regclass) NOT NULL,
    person_name text NOT NULL,
    person_imdb_id character varying(10)
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
-- Name: user; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE "user" (
    user_id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(50) NOT NULL,
    admin boolean DEFAULT false NOT NULL,
    date_added date NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public."user" OWNER TO movies;

--
-- Name: user_movie_downloaded_id_seq; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE user_movie_downloaded_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_movie_downloaded_id_seq OWNER TO movies;

--
-- Name: user_movie_favourite_id_seq; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE user_movie_favourite_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_movie_favourite_id_seq OWNER TO movies;

--
-- Name: user_movie_favourite; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE user_movie_favourite (
    user_id integer NOT NULL,
    movie_id smallint NOT NULL,
    id integer DEFAULT nextval('user_movie_favourite_id_seq'::regclass) NOT NULL
);


ALTER TABLE public.user_movie_favourite OWNER TO movies;

--
-- Name: user_movie_watched_id_seq; Type: SEQUENCE; Schema: public; Owner: movies
--

CREATE SEQUENCE user_movie_watched_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_movie_watched_id_seq OWNER TO movies;

--
-- Name: user_movie_watched; Type: TABLE; Schema: public; Owner: movies; Tablespace: 
--

CREATE TABLE user_movie_watched (
    id integer DEFAULT nextval('user_movie_watched_id_seq'::regclass) NOT NULL,
    user_id integer NOT NULL,
    movie_id integer NOT NULL,
    date_watched timestamp without time zone
);


ALTER TABLE public.user_movie_watched OWNER TO movies;

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

CREATE INDEX person_idx ON person USING btree (person_name);


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

