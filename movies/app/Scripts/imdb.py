#!/usr/bin/python
# -*- coding: utf-8 -*-
import urllib
import urllib2
import mechanize
import re
import sys
import os
import random
from bs4 import BeautifulSoup

class IMDBException(Exception):
    """
    extend base excpetion
    @todo send these exceptions to a logger
    """
    pass

class IMDB(object):

    title = None
    imdb_id = None
    page = None
    cast_page = None
    rating = False
    synopsis = None
    genres = []
    directors = []
    actors = []
    image_path = None
    release_year = None
    certificate = None
    plot_keywords = []
    rating_only = False
    runtime = 0

    def __init__(self, imdb_id, rating_only = False):
        """
        retrieves the appropriate page from imdb and parses for relevant
        content
        @param imdb_id: string
        @param rating_only: boolean - do we only need to scrape IMDB for the
                                      movie rating
        """
        self.imdb_id = str(imdb_id)
        self.genres = []
        self.directors = []
        self.actors = []
        self.plot_keywords = []
        self.rating_only = rating_only

        try:
            self.page =\
                self._get_page_mechanize('http://uk.imdb.com/title/%s' %\
                                         (self.imdb_id,))

            self._set_rating()

            if not self.rating_only and self.rating:
                self._set_title()
                self._set_certificate()
                self._set_genres()
                self._set_synopsis()
                self._set_image_path()
                self._set_release_date()
                self._set_directors()
                self._set_actors()
                self._set_runtime()
                self._set_plot_keywords()

        except Exception, e:
            print "failed", e

    def _get_page_mechanize(self, page):
        """
        retrieves the appropriate movie page from imdb using mechanize
        """
        browser = mechanize.Browser()
        browser.set_handle_robots(False)
        browser.addheaders = [('User-agent',
                               'Mozilla/5.0 (Windows NT 6.1; WOW64) '\
                               'AppleWebKit/537.4 (KHTML, like Gecko) '\
                               'Chrome/22.0.1229.94 Safari/537.4')]
        browser.open(page)
        return BeautifulSoup(browser.response().read(), "html5lib",
                             from_encoding='utf-8')

    def _set_rating(self):
        """
        sets the imdb rating for the current movie
        """
        try:
            self.rating = self.page.find('span', {'itemprop': 'ratingValue'})

            if self.rating:
                self.rating = self.rating.contents[0].strip()

        except Exception, e:
            raise IMDBException('Unable to retrieve rating(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_title(self):
        """
        gets the title for the current movie
        """
        try:
            tag = self.page.find('h1', itemprop='name').contents

            if tag:
                self.title = tag[0].strip()

        except Exception, e:
            raise IMDBException('Unable to retrieve title(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_actors(self):
        """
        sets actors for the current movie
        """
        self.cast_page =\
            self._get_page_mechanize('http://uk.imdb.com/title/%s/fullcredits' %\
                                     (self.imdb_id,))

        try:
            tags = self.cast_page.find('table', {'class': 'cast_list'}).findAll('tr')

            if tags:
                for tag in tags:
                    try:
                        actor_id = None
                        actor = None
                        image = None
                        image_src = None

                        try:
                            image = tag.find('td', {'class': 'primary_photo'}).find('img')
                        except Exception:
                            pass

                        try:
                            actor = tag.find('td', {'itemprop': 'actor'}).find('a')
                        except Exception:
                            pass

                        if actor:
                            actor_id = re.match('\/name\/(.*)\/', actor['href'])
                            actor = actor.find('span').contents[0]
                            
                            if actor_id and actor_id.group(1):
                                actor_id = actor_id.group(1)

                        if image:
                            try:
                                image_src = image['loadlate']
                            except KeyError:
                                image_src = None

                        if actor_id and actor:     
                            self.actors.append({'id': actor_id,
                                                'name': actor,
                                                'image_src': image_src})

                    except (KeyError, AttributeError), e:
                        pass

        except Exception, e:
            raise IMDBException('Unable to retrieve actors(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_directors(self):
        """
        sets directors for the current movie
        """
        try:
            tags = self.page.findAll('span', itemprop='director')
            if tags:
                for director in tags:
                    try:
                        director = director.find('a')
                        if director:
                            match = re.match('\/name\/(.*)\/?\?.*', director['href'])
                            director = director.find('span').contents[0]
                            if len(director) > 0 and match and match.group(1):
                                self.directors.append({'id':  match.group(1),
                                                       'name': director})

                    except KeyError:
                        pass
        except Exception, e:
            raise IMDBException('Unable to retrieve director(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_genres(self):
        """
        sets the genres for the current movie
        """
        try:
            genres = self.page.find('div', itemprop='genre')
            if genres:
                genres = genres.findAll('a')
                if genres:
                    for genre in genres:
                        try:
                            genre = genre.contents[0].strip()
                            if len(genre) > 0:
                                self.genres.append(genre)
                        except KeyError:
                            pass
        except Exception, e:
            raise IMDBException('Unable to retrieve genre(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_plot_keywords(self):
        """
        sets plot keywords for current movie
        @param plot_keywords_page: string
        """
        try:
            keyword_page =\
                self._get_page_mechanize('http://uk.imdb.com/title/%s/keywords' %\
                                         (self.imdb_id,))

            tags = keyword_page.find('div', {'id': 'keywords_content'}).findAll('td')
            if tags:
                for tag in tags:
                    try:
                        keyword = tag.find('a')
                        if keyword:
                            keyword = keyword.contents[0].lower().strip()
                            if len(keyword) > 0:
                                self.plot_keywords.append(keyword)
                    except KeyError:
                        pass
        except Exception, e:
            raise IMDBException('Unable to retrieve plot keywords(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_certificate(self):
        """
        sets certificate for the current movie
        """
        tag = self.page.find('meta', itemprop='contentRating')
        if tag:
            try:
                self.certificate = tag['content']
                if self.certificate.strip().lower() == 'x':
                    self.certificate = "18"
                elif (self.certificate.strip().lower() == 'r' or
                      self.certificate.strip().lower() == 'pg-13'):
                    self.certificate = "15"
                elif self.certificate.strip().lower() == 'a':
                    self.certificate = "PG"
            except KeyError:
                pass

    def _set_release_date(self):
        """
        sets the movie release date
        """
        self.release_year = self.page.find('span', {'id': 'titleYear'}).find('a')

        if self.release_year:
            match = re.match('[0-9]{4}', self.release_year.contents[0])

            if match and match.group(0):
                self.release_year = match.group(0)
            else:
                self.release_year = None

        if not self.release_year:
            match = re.match('.*\(([0-9]{4})\).*',
                             self.page.title.contents[0].string)
            if match and match.group(1):
                self.release_year = match.group(1)
            else:
                self.release_year = None

    def _set_image_path(self):
        """
        sets the poster image for the current movie
        """
        self.image_path = self.page.find('img', itemprop='image')
        if self.image_path and self.image_path['src']:
            self.image_path = self.image_path['src']

    def _set_synopsis(self):
        """
        sets the movie synopsis
        """
        try:
            tags = self.page.find('div', itemprop='description')
            if tags:
                tags = tags.contents
                self.synopsis = tags[0].strip()
        except Exception, e:
            raise IMDBException('Unable to retrieve synopsis(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_runtime(self):
        """
        sets the movie runtime
        """
        try:
            tags = self.page.find('time', itemprop='duration')
            if tags and tags.contents:
                hours = re.match('([\d]+)h', tags.contents[0].strip())

                if hours:
                    self.runtime = int(hours.group(1)) * 60

                minutes = re.match('.*?([\d]+)min', tags.contents[0].strip())

                if minutes:
                    self.runtime += int(minutes.group(1))

        except Exception, e:
            raise IMDBException('Unable to retrieve run time(%s)(%s)' %
                                (self.imdb_id, e))
