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

    rating = None

    runtime = None

    synopsis = None

    genres = []

    directors = []

    actors = []

    image_path = None

    release_year = None

    certificate = None

    plot_keywords = []

    rating_only = False

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
            self._get_page_mechanize()
            #self._get_page()
            self._set_rating()

            if not self.rating_only:
                self._set_title()
                self._set_plot_keywords()
                self._set_certificate()
                self._set_runtime()
                self._set_genres()
                self._set_synopsis()
                self._set_image_path()
                self._set_release_date()
                self._set_directors()
                self._set_actors()
        except Exception, e:
            print e
            f = open('/home/dave/'+self.imdb_id+'.html', 'w')
            f.write(str(self.page))
            f.close()

    def _get_page(self):
        """
        retrieves the appropriate movie page from imdb using mechanize
        """
        self.page = ""
        page = urllib2.urlopen('http://www.imdb.com/title/%s' % (self.imdb_id))

        for line in page:
            self.page += line

        self.page = BeautifulSoup(str(self.page))

    def _get_page_mechanize(self):
        """
        retrieves the appropriate movie page from imdb using mechanize
        """
        self.page = ""
        browser = mechanize.Browser()
        browser.set_handle_robots(False)
        browser.addheaders = [('User-agent',
                               'Mozilla/5.0 (Windows NT 6.1; WOW64) '\
                               'AppleWebKit/537.4 (KHTML, like Gecko) '\
                               'Chrome/22.0.1229.94 Safari/537.4')]
        browser.open('http://www.imdb.com/title/%s' % (self.imdb_id))
        self.page = BeautifulSoup(browser.response().read())

    def _set_rating(self):
        """
        sets the imdb rating for the current movie
        """
        try:
            self.rating = self.page.find(True, {'class': 'star-box-giga-star'})
            if self.rating:
                self.rating = self.rating.contents[0]
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

    def _set_plot_keywords(self):
        """
        sets plot keywords for current movie
        @param plot_keywords_page: string
        """
        try:
            tags = self.page.findAll('a', href=re.compile(r"/keyword/.*"))
            if tags:
                for tag in tags:
                    try:
                        self.plot_keywords.append(tag.contents[0])
                    except KeyError:
                        pass
        except Exception, e:
            raise IMDBException('Unable to retrieve plot keywords(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_certificate(self):
        """
        sets certificate for the current movie
        """
        infobar = self.page.find(True, {'class': 'infobar'})
        if infobar:
            try:
                self.certificate = infobar.contents[1]['title']
            except KeyError:
                pass

    def _set_actors(self):
        """
        sets actors for the current movie
        """
        try:
            tags = self.page.find(True, {'class': 'cast_list'}).\
                             findAll(True, {'class': 'name'})
            if tags:
                for tag in tags:
                    try:
                        self.actors.append(tag.a.contents[0])
                    except KeyError:
                        pass
        except Exception, e:
            raise IMDBException('Unable to retrieve actors(%s)(%s)' %
                                 (self.imdb_id, e))

    def _set_directors(self):
        """
        sets directors for the current movie
        """
        try:
            tags = self.page.find('a', itemprop='director')
            if tags:
                for director in tags:
                    self.directors.append(director)

        except:
            print e

    def _set_release_date(self):
        """
        sets the movie release date
        """
        self.release_year = self.page.find('time', itemprop='datePublished')
        if self.release_year and self.release_year['datetime']:
            match = re.match('[0-9]{4}', self.release_year['datetime'])
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
        self.synopsis = self.page.find('p', itemprop='description').contents
        if self.synopsis:
            self.synopsis = self.synopsis[0].strip()

    def _set_runtime(self):
        """
        sets the runtime for the current movie
        """
        offset = 0
        tags = self.page.find('time', itemprop='duration')

        if not tags:
            offset = 1
            tags = self.page.find(True, {'class': 'infobar'})

        if tags:
            self.runtime = re.sub('[^0-9]', '', str(tags.contents[offset]))

    def _set_genres(self):
        """
        sets the genres for the current movie
        """
        genres = self.page.findAll('a', itemprop='genre')
        if genres:
            for genre in genres:
                self.genres.append(genre.contents[0])
