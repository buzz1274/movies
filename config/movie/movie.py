#!/usr/bin/python2.7
import os
import re
import csv
import stat
import json
import sys
import urllib
from fabric.api import settings, hide
from fabric.operations import get
from fabric.operations import run
from sqlalchemy import *
from sqlalchemy import exc
from config import Config
from imdb import IMDB 

class Movie():
            
    config = None
        
    movie = []
    
    def __init__(self):
        """
        @author David <david@sulaco.co.uk>
        """
        try:
            self.config = Config()                       
        except Exception, e:
            sys.exit("An Error Occurred %s " % e)
                
    def scan_folders(self):
        """
        spiders the supplied drives and directories for files
        @return string 
        """
        movies = ""
        with settings(hide('running', 'stdout'), warn_only=True,
                      host_string=self.config.hostname,
                      user=self.config.username,
                      password=self.config.password):
            for path in ['movies', 'movies2']:
                 movies += \
                    run(("find %s -type f -print0 | xargs -0 ls -la" % path),
                        shell=False, pty=True, combine_stderr=True)
                    
        return movies
    
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
                self.scrape_imdb(movie.imdb_id)
                """
                imdb = IMDB(movie.imdb_id, rating_only = True)
                if imdb.rating:
                    query = self.config.movie_table.update().\
                                 where(self.config.movie_table.c.imdb_id==\
                                       movie.imdb_id).\
                                 values(imdb_rating=imdb.rating,
                                        date_last_scraped='2008-01-01')
                                        #date_last_scraped=func.now())
                    self.config.db.execute(query)
                """
                 
                    
    def update_movies(self):
        """
        parses the list of movies returned when scanning folders and
        adds, updates and deletes movies as appropriate
        """
        movies = self.scan_folders()
                                        
        if movies:
            for line in movies.split("\r\n"):  
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
            
    def due_scraping(self):
        """
        returns a list of imdb_id's that havn't already been scraped
        or havn't been scraped in the last month
        @return list
        """
        query = select([self.config.movie_table.c.imdb_id]).\
                where((self.config.movie_table.c.date_last_scraped==None).\
                __or__(func.date_part('day', func.now() - 
                                             self.config.movie_table.c.\
                                             date_last_scraped) > 28))
                                        
        return self.config.db.execute(query).fetchall()
            
    def scrape_imdb(self, imdb_id):
        """
        scrapes imdb for movie information
        @param imdb_id: string
        """
        try:
            self.movie = self.get(imdb_id)
            imdb = IMDB(imdb_id)
            query = self.config.movie_table.update().\
                                         where(self.config.movie_table.c.imdb_id==\
                                               imdb_id).\
                                         values(runtime=imdb.runtime,
                                                imdb_rating=imdb.rating,
                                                synopsis=imdb.synopsis,
                                                certificate=imdb.certificate,
                                                release_year=imdb.release_year,
                                                has_image=bool(imdb.image_path),
                                                date_last_scraped=func.now())
            self.config.db.execute(query)
            
            if imdb.image_path:
                save_path = "%s/%s.jpg" % (self.config.image_save_path, imdb_id)
                image = urllib.urlretrieve(imdb.image_path, save_path) 
                
            if imdb.directors:
                self._add_role(imdb.directors, 'director')
                
            if imdb.actors:
                self._add_role(imdb.actors, 'actor')
                                                            
            if imdb.genres:
                self._add_genre(imdb.genres)
        except:
            #@todo: this needs to be logged and dealt with
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
    
    def person(self, person_name):
        """
        gets the id for the supplied name
        """
        query = select([self.config.person_table.c.person_id]).\
                where(self.config.person_table.c.person_name==person_name)        
        person_id = self.config.db.execute(query).scalar()
        
        if not person_id:
            query = self.config.person_table.insert().values(
                                                person_name=person_name)
            self.config.db.execute(query)
            person_id = self.person(person_name)            
                
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
                
        for name in names:
            try:
                person_id = self.person(name)
                query = self.config.movie_role_table.insert().\
                                         values(movie_id=self.movie['movie_id'],
                                                person_id=person_id,
                                                role_id=role_id)
                self.config.db.execute(query)            
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
                        
    def _video_resolution(self, path):
        """
        retrieves the video resolution for the video at the supplied path
        """
        print "SCAN FILE " + path
    
                                                                 