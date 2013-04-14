#!/usr/bin/python2.7
import os
import re
import csv
import stat
import json
import sys
import urllib
import subprocess
from sqlalchemy import *
from sqlalchemy import exc
from config import Config
from imdb import IMDB

class MovieException(Exception):
    pass

class Movie():

    config = None
    movie = []

    def __init__(self):
        try:
            self.config = Config()
        except Exception, e:
            sys.exit("An Error Occurred %s " % e)

    def scan_folders(self):
        """
        spiders the supplied drives and directories for files
        @return string
        """
        movies = []
        p = os.popen('find %s -type f -print0 | xargs -0 ls -l' % (self.config.path,))
        while True:
            line = p.readline()
            if not line:
                break
            movies.append(line)

        return movies

    def find_invalid_movies(self):
        """
        scans the database for movies that have incomplete data
        @return dictionary
        """
        movie_actor_query = select([self.config.movie_role_table.c.movie_id],
                                   self.config.movie_role_table.c.role_id ==
                                   self.config.role_table.c.role_id).\
                            where(self.config.role_table.c.role == 'actor')
        movie_director_query = select([self.config.movie_role_table.c.movie_id],
                                   self.config.movie_role_table.c.role_id ==
                                   self.config.role_table.c.role_id).\
                            where(self.config.role_table.c.role == 'director')
        movie_genre_query = select([self.config.movie_genre_table.c.movie_id])
        movie_keyword_query = select([self.config.movie_keyword_table.c.movie_id])
        query = select([self.config.movie_table.c.movie_id,
                        self.config.movie_table.c.imdb_id,
                        self.config.movie_table.c.title]).\
                where((not_(self.config.movie_table.c.movie_id.in_(movie_actor_query))).\
                __or__(not_(self.config.movie_table.c.movie_id.in_(movie_director_query))).\
                __or__(not_(self.config.movie_table.c.movie_id.in_(movie_genre_query))).\
                __or__(not_(self.config.movie_table.c.movie_id.in_(movie_keyword_query))).\
                __or__(self.config.movie_table.c.certificate_id == None).\
                __or__(self.config.movie_table.c.date_last_scanned == None).\
                __or__(self.config.movie_table.c.imdb_rating == None).\
                __or__(self.config.movie_table.c.synopsis == None).\
                __or__(self.config.movie_table.c.release_year == None).\
                __or__(self.config.movie_table.c.runtime == None).\
                __or__(self.config.movie_table.c.runtime < 60).\
                __or__(self.config.movie_table.c.runtime > 600)).\
                order_by(asc(self.config.movie_table.c.title))

        return self.config.db.execute(query).fetchall()

    def find_missing_images(self):
        """
        finds movies with missing cover images
        @return: dictionary
        """
        movies_without_image = []
        query = select([self.config.movie_table.c.movie_id,
                        self.config.movie_table.c.imdb_id,
                        self.config.movie_table.c.title]).\
                where(self.config.movie_table.c.deleted == False)

        movies = self.config.db.execute(query).fetchall()
        for movie in movies:
            if not os.path.exists(self.config.image_save_path+'/'+movie.imdb_id+'.jpg'):
                movies_without_image.append(movie)

        return movies_without_image

    def rename_movie(self, old_path, new_path):
        """
        renames a movie
        @param old_path: string
        @param new_path: string
        """
        with settings(hide('running', 'stdout'), warn_only=True,
                      host_string=self.config.hostname,
                      user=self.config.username,
                      password=self.config.password):
            run(('mv "%s" "%s"' % (old_path, new_path)),
                shell=False, pty=True, combine_stderr=True)

    def update_rating(self):
        """
        updates the rating for all movies that havn't been scraped in the
        last 28days with
        """
        movies = self.due_scraping()

        if movies:
            for movie in movies:
                self.movie = movie
                imdb = IMDB(movie.imdb_id, rating_only = True)

                if imdb.rating:
                    query = self.config.movie_table.update().\
                                 where(self.config.movie_table.c.imdb_id==\
                                       movie.imdb_id).\
                                 values(imdb_rating=imdb.rating,
                                        date_last_scraped=func.now())
                    self.config.db.execute(query)

    def update_movies(self):
        """
        parses the list of movies returned when scanning folders and
        adds, updates and deletes movies as appropriate
        """
        movies = self.scan_folders()
        if movies:
            for line in movies:
                line = re.search(self.config.regex_pattern, line)
                if (line and line.group(1) and line.group(2) and
                    line.group(3) and line.group(4) and
                    line.group(5) and re.match("[^0-9]", line.group(5))):

                    title = line.group(4)
                    path = line.group(2)
                    filesize = line.group(1)
                    imdb_id = line.group(5)

                    self.movie = self.get(imdb_id)

                    if self.movie:
                        if int(self.movie['filesize']) != int(filesize):
                            self._video_resolution(path)
                        query = self.config.movie_table.update().\
                                     where(self.config.movie_table.c.imdb_id==\
                                           imdb_id).\
                                     values(filesize=filesize,
                                            path=path,
                                            date_last_scanned=func.now())
                        self.config.db.execute(query)
                    else:
                        self._video_resolution(path)
                        query = self.config.movie_table.insert().\
                                     values(imdb_id=imdb_id,
                                            filesize=filesize,
                                            path=path,
                                            title=title,
                                            date_last_scanned=func.now())
                        self.config.db.execute(query)
                        self.movie = self.get(imdb_id)
                        self.scrape_imdb(imdb_id)

            #mark as deleted all movie not seen today

    def update_invalid_movies(self):
        """
        attempts to scrape missing data for any movies without full
        data
        """
        invalid_movies = self.find_invalid_movies()
        if invalid_movies:
            for movie in invalid_movies:
                try:
                    self.scrape_imdb(movie.imdb_id)
                except Exception, e:
                    pass

    def due_scraping(self):
        """
        returns a list of imdb_id's that havn't already been scraped
        or havn't been scraped in the last 3 months
        @return list
        """
        query = select([self.config.movie_table.c.imdb_id,
                        self.config.movie_table.c.movie_id]).\
                where((self.config.movie_table.c.date_last_scraped==None).\
                __or__(func.date_part('day', func.now() -
                                             self.config.movie_table.c.\
                                             date_last_scraped) > 90))

        return self.config.db.execute(query).fetchall()

    def scrape_imdb(self, imdb_id):
        """
        scrapes imdb for movie information
        @param imdb_id: string
        """
        try:
            self.movie = self.get(imdb_id)
            imdb = IMDB(imdb_id)
            if imdb.title:
                certificate_query =\
                    select([self.config.certificate_table.c.certificate_id]).\
                    where(self.config.certificate_table.c.certificate==imdb.certificate)

                query = self.config.movie_table.update().\
                                where(self.config.movie_table.c.imdb_id==\
                                      imdb_id).\
                                values(title=imdb.title,
                                       runtime=imdb.runtime,
                                       imdb_rating=imdb.rating,
                                       certificate_id=certificate_query,
                                       synopsis=imdb.synopsis,
                                       release_year=imdb.release_year,
                                       has_image=bool(imdb.image_path),
                                       date_last_scraped=func.now())

                self.config.db.execute(query)
                save_path = "%s/%s/%s.jpg" % (self.config.image_save_path, 'movies', imdb_id)

                if not os.path.isfile(save_path) and imdb.image_path:
                    image = urllib.urlretrieve(imdb.image_path, save_path)

                    if not os.path.isfile(save_path):
                        raise MovieException('error saving movie image(%s)' %
                                               (self.imdb_id))

                if imdb.directors:
                    self._add_role(imdb.directors, 'director')

                if imdb.actors:
                    self._add_role(imdb.actors, 'actor')

                if imdb.genres:
                    self._add_genre(imdb.genres)

                if imdb.plot_keywords:
                    self._add_keywords(imdb.plot_keywords)

        except Exception, e:
            pass

    def get(self, imdb_id):
        """
        get the movie that matches the supplied imdb_id
        @param imdb_id: string
        @return: dictionary
        """
        query = select([self.config.movie_table.c.movie_id,
                        self.config.movie_table.c.imdb_id,
                        self.config.movie_table.c.path,
                        self.config.movie_table.c.filesize]).\
                where(self.config.movie_table.c.imdb_id==imdb_id)

        return self.config.db.execute(query).fetchone()

    def person(self, person):
        """
        gets the id for the supplied name
        """
        query = select([self.config.person_table.c.person_id]).\
                where(self.config.person_table.c.person_imdb_id==person['id'])
        person_id = self.config.db.execute(query).scalar()

        if not person_id:
            query = self.config.person_table.insert().values(
                                                person_name=person['name'],
                                                person_imdb_id=person['id'])
            self.config.db.execute(query)
            person_id = self.person(person)

        return person_id

    def genre(self, genre):
        """
        gets the genre ID for the supplied genre.If the genre doesn't
        exist it will be added
        """
        query = select([self.config.genre_table.c.genre_id]).\
                where(self.config.genre_table.c.genre==genre)
        genre_id = self.config.db.execute(query).scalar()

        if not genre_id:
            query = self.config.genre_table.insert().values(genre=genre)
            self.config.db.execute(query)
            genre_id = self.genre(genre)

        return genre_id

    def _add_role(self, names, role):
        """
        adds a new role to the current movie
        @param names: list
        @param role: type of role to add
        """
        query = select([self.config.role_table.c.role_id]).\
                where(self.config.role_table.c.role==role)
        role_id = self.config.db.execute(query).scalar()
        i = 0

        for name in names:
            try:
                i = i + 1
                person_id = self.person(name)
                query = self.config.movie_role_table.insert().\
                                         values(movie_id=self.movie['movie_id'],
                                                person_id=person_id,
                                                order=i,
                                                role_id=role_id)
                self.config.db.execute(query)

                if role == 'actor' and name['image_src']:
                    save_path = "%s/%s/%s.jpg" %\
                                   (self.config.image_save_path,
                                    'cast', name['id'],)

                    if not os.path.isfile(save_path) and name['image_src']:
                        image = urllib.urlretrieve(name['image_src'], save_path)

                        if not os.path.isfile(save_path):
                            raise MovieException('error saving cast image(%s)' %
                                                  (self.imdb_id))

            except exc.IntegrityError:
                pass

    def _add_genre(self, genres):
        """
        adds genres to current movie
        @param genres: list
        """
        for genre in genres:
            try:
                query = self.config.movie_genre_table.insert().\
                                     values(movie_id=self.movie['movie_id'],
                                            genre_id=self.genre(genre))
                self.config.db.execute(query)
            except exc.IntegrityError:
                pass

    def _add_keywords(self, keywords):
        """
        adds keywords for the current movie
        @param genres: list
        """
        for keyword in keywords:
            try:
                query = self.config.movie_keyword_table.insert().\
                                     values(movie_id=self.movie['movie_id'],
                                            keyword_id=self.keyword(keyword))
                self.config.db.execute(query)
            except exc.IntegrityError:
                pass

    def keyword(self, keyword):
        """
        gets the ID for the supplied keyword, if it doesn't exist it will
        be added to the database
        @param keyword: string
        @return keyword_id: integer
        """
        query = select([self.config.keyword_table.c.keyword_id]).\
                where(self.config.keyword_table.c.keyword==keyword)
        keyword_id = self.config.db.execute(query).scalar()

        if not keyword_id:
            query = self.config.keyword_table.insert().values(keyword=keyword)
            self.config.db.execute(query)
            keyword_id = self.keyword(keyword)

        return keyword_id

    def _video_resolution(self, path):
        """
        retrieves the video resolution for the video at the supplied path
        """
        print "SCAN FILE " + path

