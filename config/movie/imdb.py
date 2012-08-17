import urllib
import mechanize
import re
import sys
from BeautifulSoup import BeautifulSoup

class IMDB(object):
    
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
        self.rating_only = rating_only
        browser = mechanize.Browser()
        browser.set_handle_robots(False)
        browser.addheaders = [('User-agent', 
                               'Mozilla/5.0 (X11; U; Linux i686; '\
                               'en-US; rv:1.9.0.1) Gecko/2008071615 '\
                               'Fedora/3.0.1-1.fc9 Firefox/3.0.1')]
        browser.open('http://www.imdb.com/title/%s' % (self.imdb_id))
        self.page = BeautifulSoup(browser.response().read())       
        self._parse()
        
    def _parse(self):
        """
        parses the current imdb page for required information
        """        
        self._set_rating()
                
        if not self.rating_only:        
            self._set_runtime()
            self._set_genres()
            self._set_synopsis()        
            self._set_image_path()
            self._set_release_date()
            self._set_certificate()
            self._set_directors()
            self._set_actors()
            
            #@todo parse actor and directors from full crew page
            #@todo fix html entities in all text
            #@todo rename results returned from beautiful soup to something more
            #      sensible
                                                    
    def _set_actors(self):
        """
        sets actors for the current movie
        """
        tags = self.page.find(True, {'class': 'cast_list'}).\
                         findAll(True, {'class': 'name'})
        if tags:
            for tag in tags:
                try:
                    self.actors.append(tag.a.contents[0])
                except KeyError:
                    pass
                           
    def _set_directors(self):
        """
        sets directors for the current movie
        """
        tags = self.page.find('a', itemprop='director')
        if tags:
            for director in tags:
                self.directors.append(director)
                                
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
                                
    def _set_rating(self):
        """
        sets the imdb rating for the current movie
        """
        self.rating = self.page.find(True, {'class': 'star-box-giga-star'})
        if self.rating:
            self.rating = self.rating.contents[0]
            
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
        